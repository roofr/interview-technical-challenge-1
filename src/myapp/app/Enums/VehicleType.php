<?php

namespace App\Enums;

enum VehicleType: string
{
    case MOTORCYCLE = 'motorcycle';
    case CAR = 'car';
    case VAN = 'van';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
