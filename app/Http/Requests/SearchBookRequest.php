<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'author_id' => 'nullable|exists:authors,id',
            'min_rating' => 'nullable|numeric|min:1|max:5',
            'sort_by' => 'nullable|in:date,title,popularity',
            'order' => 'nullable|in:asc,desc'
        ];
    }
}
