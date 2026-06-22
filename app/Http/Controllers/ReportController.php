<?php

namespace App\Http\Controllers;

use App\Events\ReportUpdated;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Resources\GeoJsonCollection;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    #[QueryParameter('q', description: 'The search query to filter reports by title or description', type: 'string')]
    #[QueryParameter('status', description: 'The status filter (pending, in_progress, resolved, rejected)', type: 'string')]
    #[QueryParameter('facility_id', description: 'Filter by facility ID', type: 'integer')]
    #[QueryParameter('format', description: 'The response format (use \'geojson\' for GeoJSON)', type: 'string')]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Report::class);

        $query = Report::with(['user', 'facility.category'])
            ->filter($request->only(['q']));

        // Role-based filtering
        if ($request->user()->role === 'student') {
            $query->where('user_id', $request->user()->id);
        }

        // Additional filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->boolean('unseen_only')) {
            $query->unseenBy($request->user()->id);
        }

        if ($request->query('format') === 'geojson') {
            $reports = $query->latest()->get();

            return new GeoJsonCollection(ReportResource::collection($reports));
        }

        $reports = $query->latest()->paginate(10);

        // Status counts should be calculated based on the base query (excluding status filter)
        $countsQuery = Report::with(['user', 'facility.category'])
            ->filter($request->only(['q']));

        if ($request->user()->role === 'student') {
            $countsQuery->where('user_id', $request->user()->id);
        }

        if ($request->has('facility_id')) {
            $countsQuery->where('facility_id', $request->facility_id);
        }

        if ($request->boolean('unseen_only')) {
            $countsQuery->unseenBy($request->user()->id);
        }

        return ReportResource::collection($reports)->additional([
            'status_counts' => [
                'pending' => (clone $countsQuery)->where('status', 'pending')->count(),
                'in_progress' => (clone $countsQuery)->where('status', 'in_progress')->count(),
                'resolved' => (clone $countsQuery)->where('status', 'resolved')->count(),
                'rejected' => (clone $countsQuery)->where('status', 'rejected')->count(),
            ],
        ]);
    }

    /**
     * Mark a report as seen by the current user.
     */
    public function markAsSeen(Request $request, Report $report)
    {
        $report->seenBy()->syncWithoutDetaching([$request->user()->id]);

        return response()->json(['message' => 'Report marked as seen']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', config('filesystems.report_disk'));
        }

        $report = Report::unguarded(function () use ($request, $validated, $imagePath) {
            $coordinates = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)
                ? $this->coordinateAttributes((float) $validated['longitude'], (float) $validated['latitude'])
                : [
                    'lat_report' => (float) $validated['latitude'],
                    'long_report' => (float) $validated['longitude'],
                ];

            return Report::create([
                'user_id' => $request->user()->id,
                'facility_id' => $validated['facility_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'image_path' => $imagePath,
                'status' => 'pending',
                ...$coordinates,
            ]);
        });

        return (new ReportResource($report->refresh()->load(['user', 'facility.category'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        $this->authorize('view', $report);

        return new ReportResource($report->load(['user', 'facility.category']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        $validated = $request->validated();

        $report->update([
            'status' => $validated['status'],
        ]);

        // Reset 'seen' status for the report owner (student) when status is updated
        $report->seenBy()->detach($report->user_id);

        event(new ReportUpdated($report));

        return new ReportResource($report->load(['user', 'facility.category']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);
        $report->delete();

        event(new ReportUpdated($report));

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
