<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\GeoJsonCollection;
use App\Models\Facility;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Facility::with(['category', 'building']);

        if ($request->query('format') === 'geojson') {
            $facilities = $query->get();

            return new GeoJsonCollection(FacilityResource::collection($facilities));
        }

        $facilities = $query->paginate(15);

        return FacilityResource::collection($facilities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @bodyParam coordinate object required The geo-coordinates of the facility.
     */
    public function store(StoreFacilityRequest $request)
    {
        $validated = $request->validated();

        $facility = Facility::create([
            'category_id' => $validated['category_id'],
            'building_id' => $validated['building_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'location' => DB::raw("ST_GeomFromText('POINT(".$validated['coordinate']['lng'].' '.$validated['coordinate']['lat'].")', 4326)"),
        ]);

        return (new FacilityResource($facility->refresh()->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        return new FacilityResource($facility->load('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @bodyParam coordinate object required The geo-coordinates of the facility.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility)
    {
        $validated = $request->validated();

        $data = [
            'category_id' => $validated['category_id'],
            'building_id' => $validated['building_id'] ?? $facility->building_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if (isset($validated['coordinate']['lat']) && isset($validated['coordinate']['lng'])) {
            $data['location'] = DB::raw("ST_GeomFromText('POINT(".$validated['coordinate']['lng'].' '.$validated['coordinate']['lat'].")', 4326)");
        }

        $facility->update($data);

        return new FacilityResource($facility->load('category'));
    }

    /**
     * Search for facilities.
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        $facilities = Facility::search($query)->get();

        if ($request->query('format') === 'geojson') {
            return new GeoJsonCollection(FacilityResource::collection($facilities));
        }

        return FacilityResource::collection($facilities);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        $this->authorize('delete', $facility);

        if ($facility->reports()->exists()) {
            return response()->json([
                'message' => 'Cannot delete facility because it has associated reports.',
            ], 422);
        }

        $facility->delete();

        return response()->json([
            'message' => 'Facility deleted successfully.',
        ]);
    }
}
