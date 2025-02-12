<?php

namespace App\Http\Requests;

use App\Enums\NotificationTypesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('location'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'notify_by' => ['required', 'array'],
            'notify_by.*' => ['nullable', Rule::in([null, ...getEnumCases(NotificationTypesEnum::cases())])],
        ];
    }
}
