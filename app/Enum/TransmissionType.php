<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum TransmissionType: string
{
    case MANUAL = 'manual';
    case AUTOMATIC = 'automatic';

     public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
