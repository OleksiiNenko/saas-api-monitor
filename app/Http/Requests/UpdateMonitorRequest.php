<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMonitorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'url' => ['sometimes', 'required', 'url', 'max:2048'],
            'method' => ['sometimes', Rule::in(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'])],
            'expected_status' => ['sometimes', 'integer', 'between:100,599'],
            'interval_seconds' => ['sometimes', 'integer', 'min:30'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
