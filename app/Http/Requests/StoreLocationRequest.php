<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Location::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('locations')
                ->where(function ($query)  {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'coordinates' => ['required', 'array'],
            'coordinates.lat' => ['required', 'numeric', 'between:-90,90'],
            'coordinates.lon' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'This location is already added.',
            'coordinates.lat.required' => 'Latitude is required.',
            'coordinates.lon.required' => 'Longitude is required.',
            'coordinates.lat.between' => 'Latitude must be between -90 and 90.',
            'coordinates.lon.between' => 'Longitude must be between -180 and 180.',
        ];
    }
}
