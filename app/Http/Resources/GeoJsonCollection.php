<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GeoJsonCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => $this->collection->map(function ($resource) use ($request) {
                // If it's a JsonResource, call toArray. If it's a model, we might need a resource.
                // But in our controllers we pass models.
                // To be consistent, let's assume we want to use the resource transformation.
                return $resource->toArray($request);
            })->all(),
        ];
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  Request  $request
     * @param  JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $data = $response->getData(true);
        if (isset($data['data'])) {
            $response->setData($data['data']);
        }
    }
}
