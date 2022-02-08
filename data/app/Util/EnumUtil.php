<?php

namespace App\Util;

use BackedEnum;
use UnitEnum;

class EnumUtil
{
    public static function getEnumValues(string $enumClass): array
    {
        if (!static::isBackedEnum($enumClass)) {
            throw new \Exception('Provided string is not a backed enum class');
        }

        return array_map(function ($item) { return $item->value; }, $enumClass::cases());
    }

    public static function getEnumNames(string $enumClass): array
    {
        if (!static::isEnum($enumClass)) {
            throw new \Exception('Provided string is not an enum class');
        }

        return array_map(function ($item) { return $item->name; }, $enumClass::cases());
    }

    public static function isEnum(string $enumClass)
    {
        $implements = class_implements($enumClass);

        return isset($implements[UnitEnum::class]);
    }

    public static function isBackedEnum(string $enumClass)
    {
        $implements = class_implements($enumClass);

        return isset($implements[BackedEnum::class]);
    }
}
