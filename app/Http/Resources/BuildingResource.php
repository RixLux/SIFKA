<?php

namespace App\Http\Resources;

use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
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
                'lat' => $latitude,
                'lng' => $longitude,
            ],
            'amenities' => FacilityResource::collection($this->whenLoaded('facilities')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
