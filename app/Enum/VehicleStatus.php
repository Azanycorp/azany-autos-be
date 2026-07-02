<?php

namespace App\Enum;

enum VehicleStatus: string
{
    case ACTIVE = 'active';
    case LIVE = 'live';
    case BLOCKED = 'blocked';
    case DELETED = 'deleted';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
    case SOLD = 'sold';
    case CLOSED = 'closed';
}
