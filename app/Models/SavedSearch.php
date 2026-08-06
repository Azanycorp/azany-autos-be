<?php

namespace App\Models;

use App\Enum\VehicleStatus;
use Database\Factories\SavedSearchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    /** @use HasFactory<SavedSearchFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'is_notify' => 'boolean',
        ];
    }

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

    /**
     * Get the query builder for matching vehicles.
     *
     * @return Builder<Vehicle>
     */
    public function matchesQuery(): Builder
    {
        /** @var array<string, mixed> $filters */
        $filters = $this->filters ?? [];

        $make = $filters['make'] ?? null;
        $model = $filters['model'] ?? null;
        $minPrice = $filters['min_price'] ?? null;
        $maxPrice = $filters['max_price'] ?? null;
        $minYear = $filters['min_year'] ?? null;

        return Vehicle::query()
            ->where('status', VehicleStatus::ACTIVE->value)
            ->unless(blank($make), fn (Builder $q) => $q->where('make', $make))
            ->unless(blank($model), fn (Builder $q) => $q->where('model', $model))
            ->unless(blank($minPrice), fn (Builder $q) => $q->where('price', '>=', $minPrice))
            ->unless(blank($maxPrice), fn (Builder $q) => $q->where('price', '<=', $maxPrice))
            ->unless(blank($minYear), fn (Builder $q) => $q->where('year', '>=', $minYear));
    }

    /**
     * Get the total count of matching vehicles.
     *
     * @return Attribute<int, null>
     */
    protected function totalMatches(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->matchesQuery()->count()
        );
    }

    /**
     * Get the count of new matching vehicles created today.
     *
     * @return Attribute<int, null>
     */
    protected function newMatchesToday(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->matchesQuery()
                ->whereDate('created_at', today())
                ->count()
        );
    }
}
