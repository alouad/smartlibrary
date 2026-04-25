<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription d'un lecteur
     */
    public function registerReader(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:readers',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reader = Reader::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $reader->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $reader
        ], 201);
    }

    /**
     * Inscription d'un auteur
     */
    public function registerAuthor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:authors',
            'password' => 'required|string|min:8|confirmed',
            'bio' => 'nullable|string',
        ]);

        $author = Author::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bio' => $request->bio,
        ]);

        $token = $author->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $author
        ], 201);
    }

    /**
     * Connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'type' => 'required|in:reader,author'
        ]);

        $model = $request->type === 'reader' ? Reader::class : Author::class;
        $user = $model::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // Supprimer les anciens tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'type' => $request->type
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Profil utilisateur
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Charger les relations selon le type
        if ($user instanceof Reader) {
            $user->load(['favorites', 'ratings']);
            $user->favorites_count = $user->favorites()->count();
            $user->ratings_count = $user->ratings()->count();
        } elseif ($user instanceof Author) {
            $user->load(['books']);
            $user->books_count = $user->books()->count();
            $user->total_views = $user->books()->sum('views_count');
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}