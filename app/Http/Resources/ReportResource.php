<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReportResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'status' => $this->status,
            'coordinates' => [
                'latitude' => (float) $this->lat_report,
                'longitude' => (float) $this->long_report,
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
