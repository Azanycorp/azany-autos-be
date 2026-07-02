<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum AccidentType: string
{
    case NO_DAMAGE = 'no_accident';
    case MINOR_DAMAGE = 'minor_accident';
    case MAJOR_DAMAGE = 'major_accident';

     public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
