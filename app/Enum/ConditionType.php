<?php

namespace App\Enum;

use Illuminate\Support\Collection;

enum ConditionType: string
{
    case NEW = 'new';
    case USED = 'used';

    public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
