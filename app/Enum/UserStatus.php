<?php

namespace App\Enum;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';
    case DELETED = 'deleted';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
    case VERIFIED = 'verified';
    case UNVERIFIED = 'unverified';
    case CLOSED = 'closed';
    case TERMINATED = 'terminated';
}
