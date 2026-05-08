<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $reports = $query->latest()->paginate(15);

        return ReportResource::collection($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        $report = Report::create([
            'user_id' => $request->user()->id,
            'facility_id' => $validated['facility_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'status' => 'pending',
            'lat_report' => $validated['latitude'],
            'long_report' => $validated['longitude'],
        ]);

        return new ReportResource($report->load(['user', 'facility.category']));
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
    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,rejected',
        ]);

        $report->update([
            'status' => $validated['status'],
        ]);

        return new ReportResource($report->load(['user', 'facility.category']));
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
