<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureTag whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'user_id',
    'name',
])]
class FeatureTag extends Model
{
    //
}
