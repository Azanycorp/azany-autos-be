<?php

namespace App\Enum;
use Illuminate\Support\Collection;

enum FuelType: string
{
    case PETROL = 'petrol';
    case DIESEL = 'diesel';
    case ELECTRIC = 'electric';
    case HYBRID = 'hybrid';

     public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
