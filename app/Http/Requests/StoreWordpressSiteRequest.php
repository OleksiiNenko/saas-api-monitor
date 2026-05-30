<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWordpressSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'base_url' => ['required', 'url', 'max:2048'],
            'username' => ['required', 'string', 'max:255'],
            'app_password' => ['required', 'string', 'max:255'],
        ];
    }
}
