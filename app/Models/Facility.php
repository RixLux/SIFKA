<?php

namespace App\Models;

use App\Casts\AsGeometry;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Expression;
use Laravel\Scout\Searchable;

#[Fillable(['category_id', 'building_id', 'name', 'description', 'location'])]
class Facility extends Model
{
    use HasFactory, Searchable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => AsGeometry::class,
        ];
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $location = $this->location;
        $lat = ($location instanceof Expression || ! isset($location->latitude)) ? 0 : (float) $location->latitude;
        $lng = ($location instanceof Expression || ! isset($location->longitude)) ? 0 : (float) $location->longitude;

        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category_name' => $this->category?->name,
            'building_name' => $this->building?->name,
            '_geo' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
        ];
    }

    /**
     * Get the building that owns the facility.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the category that owns the facility.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the reports for the facility.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
