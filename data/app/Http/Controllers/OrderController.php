<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Structure\Enum\DeliveryType;
use App\Structure\Enum\OrderStatus;
use App\Util\OrderIdentifierUtil;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Validator;

class OrderController extends Controller
{
    protected static function getListDefaultParameters()
    {
        return [
            'start_date' => null,
            'end_date' => new \DateTime()
        ];
    }

    public function create(Request $request): JsonResponse
    {
        $data = $this->validate($request, [
            'buyer_name' => 'required|max:255',
            'buyer_email' => 'required|email|max:255',
            'delivery_type' => ['required', new Enum(DeliveryType::class)],
            'billing_address' => 'required|array|min:1',
            'billing_address.name' => 'required|max:255',
            'billing_address.address' => 'required|max:255',
            'billing_address.city' => 'required|max:255',
            'billing_address.zip' => 'required|digits:4',
            'shipping_address' => 'array|nullable|required_unless:ship_to_billing,true',
            'shipping_address.name' => 'required_unless:ship_to_billing,true|max:255',
            'shipping_address.address' => 'required_unless:ship_to_billing,true|max:255',
            'shipping_address.city' => 'required_unless:ship_to_billing,true|max:255',
            'shipping_address.zip' => 'required_unless:ship_to_billing,true|digits:4',
            'ship_to_billing' => 'nullable|boolean',
            'products' => 'required|array|min:1',
            'products.*.name' => 'required|max:255',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|integer|min:1',
        ]);

        $data['ship_to_billing'] = $request->boolean('ship_to_billing');

        $order = new Order();
        try {
            DB::transaction(function () use ($data, $order) {
                $order->fill($data);
                $order->status = OrderStatus::NEW;

                $billingAddress = new Address();
                $billingAddress->fill($data['billing_address'])->save();

                $order->billingAddress()->associate($billingAddress);
                if ($data['ship_to_billing']) {
                    $order->shippingAddress()->associate($order->billingAddress);
                } else {
                    $shippingAddress = new Address();
                    $shippingAddress->fill($data['billing_address'])->save();
                    $order->shippingAddress()->associate($shippingAddress);
                }

                $order->save();
                foreach ($data['products'] as $productData) {
                    $product = (new Product())->fill($productData);
                    $product->save();

                    $orderItem = (new OrderItem())->fill($productData);
                    $orderItem->product()->associate($product);
                    $orderItem->order()->associate($order);
                    $orderItem->save();
                }

                $order->save();
            });
        } catch (\Throwable $th) {
            return new JsonResponse([
                'message' => $th->getMessage()
            ], 500);
        }


        return new JsonResponse([
            'orderId' => $order->orderId
        ]);
    }

    public function update(Request $request, string $orderId): JsonResponse
    {
        $this->validate($request, [
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $order = Order::findByOrderId($orderId);
        if (!$order) {
            return new JsonResponse(['order_id' => ['Order not found']], 404);
        }

        $order->status = OrderStatus::from($request->json('status'));
        $order->save();

        return new JsonResponse('Ok');
    }

    public function list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'string|numeric',
            'status' =>  [new Enum(OrderStatus::class)],
            'start_date' => 'date|before_or_equal:now',
            'end_date' => 'date|before_or_equal:now'
        ]);
        $validator->sometimes('start_date', 'before_or_equal:end_date', function ($input) {
            return isset($input['end_date']);
        });

        $data = $this->validateWithValidator($request, $validator);

        foreach (static::getListDefaultParameters() as $field => $value) {
            $data[$field] = $data[$field] ?? $value;
        }

        $eqb = Order::with('orderItems')->with('orderItems.product')->byDate($data['start_date'], $data['end_date']);

        if (isset($data['order_id'])) {
            $eqb->where(OrderIdentifierUtil::parseOrderId($data['order_id']));
        }

        if (isset($data['status'])) {
            $eqb->whereStatus($data['status']);
        }

        $result = $this->getChunkedResults($eqb, $this->formatListResult(...));

        return new JsonResponse($result);
    }

    protected function formatListResult(Order $order)
    {
        return [
            'order_id' => $order->order_id,
            'buyer_name' => $order->buyer_name,
            'status' => $order->status,
            'ordered_at' => $order->created_at->format('Y-m-d H:i:s'),
            'total' =>  $order->total_price
        ];
    }
}
