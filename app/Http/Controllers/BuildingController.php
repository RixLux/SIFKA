<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Http\Resources\GeoJsonCollection;
use App\Models\Building;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuildingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Building::with('facilities');

        if ($request->query('format') === 'geojson') {
            $buildings = $query->get();

            return new GeoJsonCollection(BuildingResource::collection($buildings));
        }

        $buildings = $query->paginate(15);

        return BuildingResource::collection($buildings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuildingRequest $request)
    {
        $validated = $request->validated();

        $building = Building::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'location' => DB::raw("ST_GeomFromText('POINT(".$validated['longitude'].' '.$validated['latitude'].")', 4326)"),
        ]);

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
            $data['location'] = DB::raw("ST_GeomFromText('POINT(".$validated['longitude'].' '.$validated['latitude'].")', 4326)");
        }

        $building->update($data);

        return new BuildingResource($building);
    }

    /**
     * Search for buildings.
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        $buildings = Building::search($query)->get();

        if ($request->query('format') === 'geojson') {
            return new GeoJsonCollection(BuildingResource::collection($buildings));
        }

        return BuildingResource::collection($buildings);
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
