<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Author;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file' => 'required|file|mimes:pdf|max:10000',
            'published_date' => 'nullable|date',
            'isbn' => 'nullable|string|max:20|unique:books,isbn',
            'page_count' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:50',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}