<?php

namespace App\Http\Requests;

use App\Models\Facility;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * @bodyParam coordinate object required The geo-coordinates of the facility.
 * @bodyParam coordinate.lat float required Latitude. Example: -0.9482
 * @bodyParam coordinate.lng float required Longitude. Example: 100.3606
 */
class StoreFacilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Facility::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'building_id' => 'nullable|exists:buildings,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'coordinate' => 'required|array',
            'coordinate.lat' => 'required|numeric',
            'coordinate.lng' => 'required|numeric',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Handle cases where flat parameters are sent but we want to enforce nested structure
        if ($this->has(['latitude', 'longitude']) && ! $this->has('coordinate')) {
            $this->merge([
                'coordinate' => [
                    'lat' => $this->latitude,
                    'lng' => $this->longitude,
                ],
            ]);
        }
    }
}
