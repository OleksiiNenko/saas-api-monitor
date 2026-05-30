<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneratePageRequest extends FormRequest
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
            'wordpress_site_id' => ['required', 'exists:wordpress_sites,id'],
            'topic' => ['required', 'string', 'max:500'],
            'page_type' => ['sometimes', 'string', 'max:50'],
            'audience' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tone' => ['sometimes', 'nullable', 'string', 'max:100'],
            'language' => ['sometimes', Rule::in(['ru', 'uk', 'en'])],
            'keywords' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sections' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'cta' => ['sometimes', 'nullable', 'string', 'max:255'],
            'extra_instructions' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
