<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Seul l'admin ou l'utilisateur lui-même peut modifier
        $userId = $this->route('user') ?? $this->route('id');
        return auth()->check() && 
               (auth()->id() == $userId || 
                (auth()->user() instanceof \App\Models\Reader && auth()->user()->is_admin));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');
        $userType = $this->route('type') ?? 'reader';
        
        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique($userType . 's', 'email')->ignore($userId)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        // Règles spécifiques pour l'auteur
        if ($userType === 'author') {
            $rules['bio'] = 'nullable|string|max:1000';
            $rules['website'] = 'nullable|url|max:255';
        }
        
        // Règles pour l'admin
        if (auth()->user() instanceof \App\Models\Reader && auth()->user()->is_admin) {
            $rules['is_admin'] = 'nullable|boolean';
        }
        
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'avatar.image' => 'L\'avatar doit être une image.',
            'avatar.mimes' => 'L\'avatar doit être au format JPEG, PNG ou JPG.',
            'avatar.max' => 'L\'avatar ne doit pas dépasser 2 Mo.',
            'bio.max' => 'La biographie ne doit pas dépasser 1000 caractères.',
            'website.url' => 'Le site web doit être une URL valide.',
        ];
    }
}