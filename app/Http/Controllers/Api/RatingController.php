<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Resources\RatingResource;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request)
    {
        $data = $request->validated();
        $reader = $request->user();

        // Vérifier si ce lecteur a déjà noté ce livre
        $existing = Rating::where('book_id', $data['book_id'])
            ->where('reader_id', $reader->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Vous avez déjà noté ce livre'], 400);
        }

        $rating = Rating::create([
            'book_id' => $data['book_id'],
            'reader_id' => $reader->id,
            'rating' => $data['rating'],
            'review' => $data['review'] ?? null,
        ]);

        return new RatingResource($rating->load('reader'));
    }

    public function update(Request $request, Rating $rating)
    {
        if ($request->user()->id !== $rating->reader_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $rating->update($request->only(['rating', 'review']));

        return new RatingResource($rating->load('reader'));
    }

    public function destroy(Rating $rating, Request $request)
    {
        if ($request->user()->id !== $rating->reader_id && !$request->user() instanceof \App\Models\User) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rating->delete();

        return response()->json(['message' => 'Note supprimée avec succès']);
    }
}