<?php

namespace App\Structure\Enum;

enum DeliveryType : string
{
    case DELIVERY = 'delivery';
    case PICK_UP = 'pick_up';
}