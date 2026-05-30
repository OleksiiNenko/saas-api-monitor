<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneratedPageRequest extends FormRequest
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
            'fields' => ['sometimes', 'array'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'parent_id' => ['sometimes', 'nullable', 'integer'],
            'category_ids' => ['sometimes', 'nullable', 'array'],
            'category_ids.*' => ['integer'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'author_id' => ['sometimes', 'nullable', 'integer'],
            'menu_order' => ['sometimes', 'integer'],
            'language' => ['sometimes', 'string', 'max:8'],
        ];
    }
}
