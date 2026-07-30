<?php

namespace App\Models;

use App\Enum\VehicleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'listing_type',
    'fuel_type',
    'transmission_type',
    'condition',
    'kilometer_reading',
    'make',
    'model',
    'min_year',
    'max_year',
    'min_price',
    'max_price',
    'country_id',
    'body_type',
    'filters',
    'is_notify',
])]

class SavedSearch extends Model
{
    protected $casts = [
        'filters' => 'array',
        'is_notify' => 'boolean',
    ];

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

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->where('is_active', true)
            ->when($filters['make'] ?? null, function ($q, $make) {
                $q->where('make', $make);
            })
            ->when($filters['model'] ?? null, function ($q, $model) {
                $q->where('model', $model);
            })
            ->when($filters['min_price'] ?? null, function ($q, $minPrice) {
                $q->where('price', '>=', $minPrice);
            })
            ->when($filters['max_price'] ?? null, function ($q, $maxPrice) {
                $q->where('price', '<=', $maxPrice);
            })
            ->when($filters['min_year'] ?? null, function ($q, $minYear) {
                $q->where('year', '>=', $minYear);
            });
    }

    public function matchesQuery(): Builder
    {
        $filters = $this->filters ?? [];

        return Vehicle::query()
            ->where('status', VehicleStatus::ACTIVE->value)
            ->when(! empty($filters['make']), fn ($q) => $q->where('make', $filters['make']))
            ->when(! empty($filters['model']), fn ($q) => $q->where('model', $filters['model']))
            ->when(! empty($filters['min_price']), fn ($q) => $q->where('price', '>=', $filters['min_price']))
            ->when(! empty($filters['max_price']), fn ($q) => $q->where('price', '<=', $filters['max_price']))
            ->when(! empty($filters['min_year']), fn ($q) => $q->where('year', '>=', $filters['min_year']));
    }

    public function getTotalMatchesAttribute(): int
    {
        return $this->matchesQuery()->count();
    }

    public function getNewMatchesTodayAttribute(): int
    {
        return $this->matchesQuery()
            ->whereDate('created_at', now()->today())
            ->count();
    }
}
