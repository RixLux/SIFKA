<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\FacilitySummaryResource;
use App\Http\Resources\GeoJsonCollection;
use App\Models\Facility;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    #[QueryParameter('q', description: 'The search query to filter facilities by name or description', type: 'string')]
    #[QueryParameter('format', description: 'The response format (use \'geojson\' for GeoJSON)', type: 'string')]
    public function index(Request $request)
    {
        $query = Facility::select(['id', 'category_id', 'building_id', 'name', 'description', 'location'])
            ->with(['category:id,name,icon_marker,color_code', 'building:id,name,description,location'])
            ->filter($request->only(['q']));

        if ($request->query('format') === 'geojson') {
            $facilities = $query->get();

            return new GeoJsonCollection(FacilitySummaryResource::collection($facilities));
        }

        $facilities = $query->paginate(10);

        return FacilitySummaryResource::collection($facilities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @bodyParam coordinate object required The geo-coordinates of the facility.
     */
    public function store(StoreFacilityRequest $request)
    {
        $validated = $request->validated();

        $facility = Facility::unguarded(function () use ($validated) {
            return Facility::create([
                'category_id' => $validated['category_id'],
                'building_id' => $validated['building_id'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                ...$this->coordinateAttributes((float) $validated['coordinate']['lng'], (float) $validated['coordinate']['lat']),
            ]);
        });

        return (new FacilityResource($facility->refresh()->load(['category', 'building'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        return new FacilityResource($facility->load(['category', 'building']));
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
            $data += $this->coordinateAttributes((float) $validated['coordinate']['lng'], (float) $validated['coordinate']['lat']);
        }

        $facility->update($data);

        return new FacilityResource($facility->refresh()->load(['category', 'building']));
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
