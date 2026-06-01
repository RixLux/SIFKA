<?php

namespace App\Http\Resources;

use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Safe access to spatial data, handling raw DB expressions if model wasn't refreshed
        $latitude = $this->location instanceof Expression
            ? 0
            : (float) ($this->location->latitude ?? 0);

        $longitude = $this->location instanceof Expression
            ? 0
            : (float) ($this->location->longitude ?? 0);

        if ($request->query('format') === 'geojson') {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        $longitude,
                        $latitude,
                    ],
                ],
                'properties' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'description' => $this->description,
                    'image_url' => $this->image_url,
                    'status' => $this->status,
                    'type' => 'report',
                    'user_id' => $this->user_id,
                    'facility_id' => $this->facility_id,
                    'facility_name' => $this->facility?->name,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                ],
            ];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'coordinate' => [
                'lat' => $latitude,
                'lng' => $longitude,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'facility' => new FacilityResource($this->whenLoaded('facility')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
