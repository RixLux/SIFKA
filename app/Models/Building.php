<?php

namespace App\Models;

use App\Casts\AsGeometry;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Expression;
use Laravel\Scout\Searchable;

#[Fillable(['name', 'description', 'location'])]
class Building extends Model
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
            '_geo' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
        ];
    }

    /**
     * Get the facilities for the building.
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
