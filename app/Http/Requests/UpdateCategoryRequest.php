<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && 
               auth()->user() instanceof \App\Models\Reader && 
               auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id');
        
        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($categoryId)
            ],
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:20|regex:/^#[a-f0-9]{6}$/i',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Le nom ne doit pas dépasser 100 caractères.',
            'name.unique' => 'Cette catégorie existe déjà.',
            'description.max' => 'La description ne doit pas dépasser 500 caractères.',
            'color.regex' => 'La couleur doit être au format hexadécimal (ex: #3498db).',
        ];
    }
}