<?php

namespace app\Enums;

enum ParkSpotType: string
{
    case REGULAR = 'regular';
    case MOTORCYCLE = 'motorcycle';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
