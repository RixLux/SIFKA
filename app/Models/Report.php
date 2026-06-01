<?php

namespace App\Models;

use App\Casts\AsGeometry;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

#[Fillable(['user_id', 'facility_id', 'title', 'description', 'image_path', 'status', 'location'])]
class Report extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['image_url'];

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
            'user_id' => (int) $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'user_name' => $this->user?->name,
            'facility_name' => $this->facility?->name,
            '_geo' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
        ];
    }

    /**
     * Get the user that created the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the facility associated with the report.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the absolute URL for the report's image.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_path
                ? Storage::disk(config('filesystems.report_disk'))->url($this->image_path)
                : null
        );
    }
}
