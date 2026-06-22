<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Http\Resources\BuildingSummaryResource;
use App\Http\Resources\GeoJsonCollection;
use App\Models\Building;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    #[QueryParameter('q', description: 'The search query to filter buildings by name or description', type: 'string')]
    #[QueryParameter('format', description: 'The response format (use \'geojson\' for GeoJSON)', type: 'string')]
    public function index(Request $request)
    {
        $query = Building::select(['id', 'name', 'description', 'location'])
            ->withCount('facilities')
            ->filter($request->only(['q']));

        if ($request->query('format') === 'geojson') {
            $buildings = $query->get();

            return new GeoJsonCollection(BuildingSummaryResource::collection($buildings));
        }

        $buildings = $query->paginate(10);

        return BuildingSummaryResource::collection($buildings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuildingRequest $request)
    {
        $validated = $request->validated();

        $building = Building::unguarded(function () use ($validated) {
            return Building::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                ...$this->coordinateAttributes((float) $validated['longitude'], (float) $validated['latitude']),
            ]);
        });

        return (new BuildingResource($building->refresh()))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building)
    {
        return new BuildingResource($building->load('facilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuildingRequest $request, Building $building)
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if (isset($validated['latitude']) && isset($validated['longitude'])) {
            $data += $this->coordinateAttributes((float) $validated['longitude'], (float) $validated['latitude']);
        }

        $building->update($data);

        return new BuildingResource($building->refresh()->load('facilities'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building)
    {
        $this->authorize('delete', $building);

        if ($building->facilities()->exists()) {
            return response()->json([
                'message' => 'Cannot delete building because it has associated facilities.',
            ], 422);
        }

        $building->delete();

        return response()->json([
            'message' => 'Building deleted successfully.',
        ]);
    }
}
