<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof \App\Models\Reader) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return BookResource::collection($user->favorites()->paginate(15));
    }

    public function add(Request $request, $book_id)
    {
        $user = $request->user();
        if (!$user instanceof \App\Models\Reader) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $book = Book::findOrFail($book_id);
        
        if (!$user->favorites()->where('book_id', $book->id)->exists()) {
            $user->favorites()->attach($book->id);
        }

        return response()->json(['message' => 'Livre ajouté aux favoris']);
    }

    public function remove(Request $request, $book_id)
    {
        $user = $request->user();
        if (!$user instanceof \App\Models\Reader) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->favorites()->detach($book_id);

        return response()->json(['message' => 'Livre retiré des favoris']);
    }
}