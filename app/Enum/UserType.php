<?php

namespace App\Enum;

enum UserType: string
{
    case AUTOBUYER = 'azanyauto_buyer';
    case AUTODEALER = 'azanyauto_dealer';

    public static function values(): mixed
    {
        return (new \Illuminate\Support\Collection(self::cases()))->pluck('value');
    }
}
