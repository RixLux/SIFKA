<?php

namespace App\Http\Resources;

use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
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
                    'type' => 'facility',
                    'building_id' => $this->building_id,
                    'category_id' => $this->category_id,
                    'category_name' => $this->category?->name,
                    'category_icon' => $this->category?->icon_marker,
                    'category_color' => $this->category?->color_code,
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
            'category' => new CategoryResource($this->whenLoaded('category')),
            'building_id' => $this->building_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
