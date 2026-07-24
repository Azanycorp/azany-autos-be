<?php

declare(strict_types=1);

namespace App\Enum;

enum SubscriptionStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case CANCELED = 'canceled';
    case ACTIVE = 'active';
}
