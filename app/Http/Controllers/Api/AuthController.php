<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Reader;
use App\Models\Author;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:readers,email|unique:authors,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:reader,author',
            'bio' => 'nullable|string',
            'website' => 'nullable|url'
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($request->role === 'author') {
            $userData['bio'] = $request->bio;
            $userData['website'] = $request->website;
            $user = Author::create($userData);
            $token = $user->createToken('auth_token', ['role:author'])->plainTextToken;
        } else {
            $user = Reader::create($userData);
            $token = $user->createToken('auth_token', ['role:reader'])->plainTextToken;
        }

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $request->role
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:reader,author,admin'
        ]);

        if ($request->role === 'admin') {
            $user = \App\Models\User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }
            $token = $user->createToken('auth_token', ['role:admin'])->plainTextToken;
        } elseif ($request->role === 'author') {
            $user = Author::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }
            $token = $user->createToken('auth_token', ['role:author'])->plainTextToken;
        } else {
            $user = Reader::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }
            $token = $user->createToken('auth_token', ['role:reader'])->plainTextToken;
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $request->role
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $role = 'reader';
        if ($user instanceof \App\Models\User) $role = 'admin';
        elseif ($user instanceof Author) $role = 'author';

        return response()->json([
            'user' => $user,
            'role' => $role
        ]);
    }
}
