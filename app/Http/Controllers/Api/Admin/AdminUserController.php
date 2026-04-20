<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin');
    }

    /**
     * Liste tous les utilisateurs (lecteurs et auteurs)
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        
        $readers = [];
        $authors = [];
        
        if ($type === 'all' || $type === 'readers') {
            $readers = Reader::withCount(['favorites', 'ratings'])
                             ->orderBy('created_at', 'desc')
                             ->get()
                             ->map(function($reader) {
                                 $reader->user_type = 'reader';
                                 return $reader;
                             });
        }
        
        if ($type === 'all' || $type === 'authors') {
            $authors = Author::withCount('books')
                             ->orderBy('created_at', 'desc')
                             ->get()
                             ->map(function($author) {
                                 $author->user_type = 'author';
                                 return $author;
                             });
        }
        
        $users = collect($readers)->concat($authors)->sortByDesc('created_at');
        
        return response()->json([
            'success' => true,
            'data' => $users->values()
        ]);
    }

    /**
     * Afficher un utilisateur spécifique
     */
    public function show($id)
    {
        // Chercher d'abord dans les lecteurs
        $user = Reader::with(['favorites', 'ratings'])->find($id);
        $type = 'reader';
        
        // Si pas trouvé, chercher dans les auteurs
        if (!$user) {
            $user = Author::with('books')->find($id);
            $type = 'author';
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        $user->user_type = $type;
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Modifier un utilisateur
     */
    public function update(Request $request, $id)
    {
        // Chercher d'abord dans les lecteurs
        $user = Reader::find($id);
        $type = 'reader';
        
        if (!$user) {
            $user = Author::find($id);
            $type = 'author';
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:' . $type . 's,email,' . $id,
            'password' => 'nullable|string|min:8',
            'bio' => $type === 'author' ? 'nullable|string' : 'prohibited',
            'website' => $type === 'author' ? 'nullable|url' : 'prohibited',
        ]);
        
        $data = [];
        
        if ($request->has('name')) {
            $data['name'] = $request->name;
        }
        
        if ($request->has('email')) {
            $data['email'] = $request->email;
        }
        
        if ($request->has('password') && $request->password) {
            $data['password'] = Hash::make($request->password);
        }
        
        if ($type === 'author') {
            if ($request->has('bio')) {
                $data['bio'] = $request->bio;
            }
            if ($request->has('website')) {
                $data['website'] = $request->website;
            }
        }
        
        $user->update($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $user
        ]);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy($id)
    {
        // Chercher d'abord dans les lecteurs
        $user = Reader::find($id);
        
        if (!$user) {
            $user = Author::find($id);
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        // Supprimer les tokens
        $user->tokens()->delete();
        
        // Supprimer les relations
        if ($user instanceof Reader) {
            $user->favorites()->detach();
            $user->ratings()->delete();
        } elseif ($user instanceof Author) {
            foreach ($user->books as $book) {
                $book->categories()->detach();
                $book->favorites()->detach();
                $book->ratings()->delete();
                $book->delete();
            }
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }
}