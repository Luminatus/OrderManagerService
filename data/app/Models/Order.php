<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Structure\Enum\DeliveryType;
use App\Structure\Enum\OrderStatus;
use App\Util\OrderIdentifierUtil;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'buyer_name', 'buyer_email', 'delivery_type', 'status', 'billing_address_id', 'shipping_address_id',
    ];


    protected static function booted()
    {
        static::creating(function ($order) {
            OrderIdentifierUtil::generateOrderIdSalt($order);
        });
    }

    /** Find by */

    public static function findByOrderId($orderId)
    {
        return static::where(OrderIdentifierUtil::parseOrderId($orderId))->first();
    }

    /** Relations */

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class)->withDefault();
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class)->withDefault();
    }

    /** Scopes */

    public function scopeByDate($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /** Accessors */

    public function getOrderIdAttribute()
    {
        return OrderIdentifierUtil::calculateOrderId($this);
    }

    public function getTotalPriceAttribute()
    {
        return $this->orderItems->map(fn ($oi) => $oi->total_price)->sum();
    }

    /** Mutators */

    public function setDeliveryTypeAttribute($value)
    {
        if (!$value instanceof DeliveryType) {
            $value = DeliveryType::from($value);
        }

        $this->attributes['delivery_type'] = $value;
    }

    public function setStatusAttribute($value)
    {
        if (!$value instanceof OrderStatus) {
            $value = OrderStatus::from($value);
        }

        $this->attributes['status'] = $value;
    }
}
