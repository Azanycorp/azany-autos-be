<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum ListingType: string
{
    case FOR_SALE = 'for_sale';
    case FOR_RENT = 'for_rent';

     public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
