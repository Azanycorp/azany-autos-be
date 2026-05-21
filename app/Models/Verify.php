<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // <-- 1. Import Carbon at the top

/**
 * @property Carbon $expires_at  <-- 2. This explicit line completely fixes Line 26!
 * @property Carbon $created_at
 * @property string $token
 * @property string $email
 * @property string $status
 */
#[Fillable(['user_id', 'token', 'email', 'status', 'expires_at'])]
#[Table(timestamps: true)]
class Verify extends Model
{
    const UPDATED_AT = null;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the verification token.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
