<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $book = $this->route('book');
        return $this->user()->can('update', $book);
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file' => 'nullable|file|mimes:pdf|max:10000',
            'published_date' => 'nullable|date',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $this->book->id,
            'page_count' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:50',
            'categories' => 'sometimes|required|array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}