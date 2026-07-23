<?php

namespace App\Traits;

use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserRelationships
{
    /**
     * Get the country associated with the user.
     *
     * @return BelongsTo<Country, $this>
     *                                   *
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
