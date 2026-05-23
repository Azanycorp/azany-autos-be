<?php

namespace App\Enum;

use Illuminate\Support\Collection;

enum UserType: string
{
    case AUTOBUYER = 'azanyauto_buyer';
    case AUTODEALER = 'azanyauto_dealer';

    public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
