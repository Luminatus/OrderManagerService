<?php

namespace App\Structure\Enum;

enum OrderStatus : string
{
    case NEW = 'new';
    case COMPLETED = 'completed';
}