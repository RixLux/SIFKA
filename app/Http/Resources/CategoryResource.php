<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon_marker' => $this->icon_marker,
            'color_code' => $this->color_code,
            'facilities_count' => $this->whenCounted('facilities'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
