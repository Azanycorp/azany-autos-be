<?php

namespace App\Enum;

use Illuminate\Support\Collection;

enum DamageType: string
{
    case NO_DAMAGE = 'no_damage';
    case MINOR_DAMAGE = 'minor_damage';
    case MAJOR_DAMAGE = 'major_damage';

    public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
