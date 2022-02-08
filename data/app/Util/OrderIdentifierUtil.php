<?php

namespace App\Util;

use App\Models\Order;

class OrderIdentifierUtil
{
    const CHARSET = '0123456789';
    const SALT_LENGTH = 4;

    public static function generateOrderIdSalt(?Order $order = null, bool $regenerate = false)
    {
        if ($regenerate || !$order || !$order->order_id_salt) {
            $salt = '';
            foreach (range(1, static::SALT_LENGTH) as $idx) {
                $salt .= static::CHARSET[mt_rand(0, strlen(static::CHARSET) - 1)];
            }

            if ($order) {
                $order->order_id_salt = $salt;
            }

            return $salt;
        }
    }

    public static function calculateOrderId(Order $order)
    {
        if (!$order->id) {
            return null;
        }
        if (!$order->order_id_salt || strlen($order->order_id_salt) != static::SALT_LENGTH) {
            static::generateOrderIdSalt($order, true);
        }

        $id = (string)$order->id;
        $salt = $order->order_id_salt;

        $orderId = '';
        $idLength = strlen($id);

        foreach (range(0, min($idLength, static::SALT_LENGTH) - 1) as $idx) {
            $orderId .= $salt[$idx] . $id[$idx];
        }

        if ($idLength > static::SALT_LENGTH) {
            $orderId .= substr($id, static::SALT_LENGTH);
        } elseif ($idLength < static::SALT_LENGTH) {
            $orderId .= substr($salt, $idLength);
        }

        return $orderId;
    }

    public static function parseOrderId(string $orderId)
    {
        $salt = '';
        $id = '';

        $idLength = strlen($orderId) - static::SALT_LENGTH;
        for ($i = 0; $i < min($idLength, static::SALT_LENGTH) * 2; $i += 2) {
            $salt .= $orderId[$i];
            $id .= $orderId[$i + 1];
        }

        if ($idLength > static::SALT_LENGTH) {
            $id .=  substr($orderId, static::SALT_LENGTH * 2);
        } elseif ($idLength < static::SALT_LENGTH) {
            $salt .=  substr($orderId, $idLength * 2);
        }

        return [
            'id' => $id,
            'order_id_salt' => $salt
        ];
    }
}
