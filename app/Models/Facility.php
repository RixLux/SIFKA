<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'description', 'latitude', 'longitude'])]
class Facility extends Model
{
    use HasFactory;

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
