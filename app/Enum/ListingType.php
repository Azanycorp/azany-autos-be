<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum ListingType: string
{
    case SALE = 'direct_sale';
    case AUCTION = 'auction';

     public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
