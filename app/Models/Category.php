<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

#[Fillable(['name', 'icon_marker', 'color_code'])]
class Category extends Model
{
    use HasFactory, Searchable;

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * Get the facilities for the category.
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
