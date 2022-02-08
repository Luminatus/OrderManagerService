<?php

namespace Database\Factories;

use App\Models\Order;
use App\Structure\Enum\DeliveryType;
use App\Structure\Enum\OrderStatus;
use App\Util\OrderIdentifierUtil;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id_salt' => OrderIdentifierUtil::generateOrderIdSalt(),
            'buyer_name' => $this->faker->name,
            'buyer_email' => $this->faker->unique()->safeEmail,
            'delivery_type' => $this->faker->randomElement(DeliveryType::cases()),
            'status' => $this->faker->randomElement(OrderStatus::cases()),
        ];
    }
}
