<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'latitude', 'longitude'])]
class Building extends Model
{
    use HasFactory;

    /**
     * Get the facilities for the building.
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
