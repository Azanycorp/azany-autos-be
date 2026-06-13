<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $email
 * @property \Carbon\CarbonImmutable $expires_at
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Verify whereUserId($value)
 * @mixin \Eloquent
 */

#[Fillable(['user_id', 'token', 'email', 'status', 'expires_at'])]
#[Table(timestamps: true)]

class Verify extends Model
{
    const UPDATED_AT = null;

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
