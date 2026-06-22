<?php

namespace App\Http\Resources;

use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Resolve coordinates from either spatial or legacy scalar columns.
     *
     * @return array{lat: float, lng: float}
     */
    private function coordinate(): array
    {
        $location = $this->location;

        if ($location instanceof Expression) {
            return ['lat' => 0, 'lng' => 0];
        }

        if (is_object($location)) {
            return [
                'lat' => (float) ($location->latitude ?? 0),
                'lng' => (float) ($location->longitude ?? 0),
            ];
        }

        return [
            'lat' => (float) ($this->lat_report ?? $this->latitude ?? 0),
            'lng' => (float) ($this->long_report ?? $this->longitude ?? 0),
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $coordinate = $this->coordinate();
        $longitude = $coordinate['lng'];

        $isSeen = $request->user() ? $this->seenBy()->where('user_id', $request->user()->id)->exists() : true;

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
                    'is_seen' => $isSeen,
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
                'lat' => $coordinate['lat'],
                'lng' => $coordinate['lng'],
            ],
            'is_seen' => $isSeen,
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
