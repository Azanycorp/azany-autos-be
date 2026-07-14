<?php

namespace App\Enum;

use Illuminate\Support\Collection;

enum InspectionStatus: string
{
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';
    case BLOCKED = 'blocked';
    case DELETED = 'deleted';
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case SUSPENDED = 'suspended';

    public static function values(): mixed
    {
        return (new Collection(self::cases()))->pluck('value');
    }
}
