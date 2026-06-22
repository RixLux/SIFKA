<?php

namespace App\Http\Resources;

use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
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
            'lat' => (float) ($this->latitude ?? 0),
            'lng' => (float) ($this->longitude ?? 0),
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

        if ($request->query('format') === 'geojson') {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        $longitude,
                        $coordinate['lat'],
                    ],
                ],
                'properties' => [
                    'id' => $this->id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'type' => 'building',
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                ],
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'coordinate' => [
                'lat' => $coordinate['lat'],
                'lng' => $coordinate['lng'],
            ],
            'amenities' => FacilitySummaryResource::collection($this->whenLoaded('facilities')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
