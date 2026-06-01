<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Resources\GeoJsonCollection;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Report::class);

        $query = Report::with(['user', 'facility.category']);

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

        if ($request->query('format') === 'geojson') {
            $reports = $query->latest()->get();

            return new GeoJsonCollection(ReportResource::collection($reports));
        }

        $reports = $query->latest()->paginate(15);

        return ReportResource::collection($reports)->additional([
            'status_counts' => [
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
                'resolved' => (clone $query)->where('status', 'resolved')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
            ],
        ]);
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

        $report = Report::create([
            'user_id' => $request->user()->id,
            'facility_id' => $validated['facility_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'status' => 'pending',
            'location' => DB::raw("ST_GeomFromText('POINT(".$validated['longitude'].' '.$validated['latitude'].")', 4326)"),
        ]);

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

        return new ReportResource($report->load(['user', 'facility.category']));
    }

    /**
     * Search for reports.
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Report::class);

        $query = $request->query('q');

        $search = Report::search($query);

        if ($request->user()->role === 'student') {
            $search->where('user_id', $request->user()->id);
        }

        $reports = $search->get();

        if ($request->query('format') === 'geojson') {
            return new GeoJsonCollection(ReportResource::collection($reports));
        }

        return ReportResource::collection($reports);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
