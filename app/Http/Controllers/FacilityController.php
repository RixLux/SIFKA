<?php

namespace App\Http\Controllers;

use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = Facility::with('category')->get();
        return FacilityResource::collection($facilities);
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        return new FacilityResource($facility->load('category'));
    }
}
