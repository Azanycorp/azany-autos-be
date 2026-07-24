<?php

namespace App\Enum;

use Illuminate\Support\Collection;

enum AccidentType: string
{
    case NO_ACCIDENT = 'no_accident';
    case MINOR_ACCIDENT = 'minor_accident';
    case MAJOR_ACCIDENT = 'major_accident';

    public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
