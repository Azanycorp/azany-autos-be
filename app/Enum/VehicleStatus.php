<?php

namespace App\Enum;
use Illuminate\Support\Collection;
enum VehicleStatus: string
{
    case ACTIVE = 'active';
    case LIVE = 'live';
    case BLOCKED = 'blocked';
    case DELETED = 'deleted';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
    case AVAILABLE = 'available';
    case SOLD = 'sold';
    case CLOSED = 'closed';

     public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
