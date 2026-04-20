<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Seul un admin peut créer des catégories
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
        return [
            'name' => 'required|string|max:100|unique:categories,name',
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
            'name.required' => 'Le nom de la catégorie est requis.',
            'name.max' => 'Le nom ne doit pas dépasser 100 caractères.',
            'name.unique' => 'Cette catégorie existe déjà.',
            'description.max' => 'La description ne doit pas dépasser 500 caractères.',
            'color.regex' => 'La couleur doit être au format hexadécimal (ex: #3498db).',
        ];
    }
}