<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('author');
    }

    /**
     * Liste des livres de l'auteur connecté
     */
    public function index(Request $request)
    {
        $books = auth()->user()->books()
                       ->with(['categories'])
                       ->withAvg('ratings', 'rating')
                       ->withCount('ratings')
                       ->orderBy($request->get('sort', 'created_at'), 
                                $request->get('order', 'desc'))
                       ->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * Créer un nouveau livre (automatiquement associé à l'auteur)
     */
    public function store(StoreBookRequest $request)
    {
        // Upload du fichier PDF
        $pdfPath = $request->file('file_path')->store('books/pdfs', 'public');
        
        // Upload de l'image de couverture
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('books/covers', 'public');
        }

        // Création du livre
        $book = Book::create([
            'title' => $request->title,
            'author_id' => auth()->id(),
            'description' => $request->description,
            'cover_image' => $coverPath,
            'file_path' => $pdfPath,
            'published_date' => $request->published_date,
            'isbn' => $request->isbn,
            'page_count' => $request->page_count,
            'language' => $request->language ?? 'français',
        ]);

        // Attacher les catégories
        if ($request->has('categories')) {
            $book->categories()->attach($request->categories);
        }

        return response()->json([
            'success' => true,
            'message' => 'Livre créé avec succès',
            'data' => $book->load('categories')
        ], 201);
    }

    /**
     * Afficher un de ses livres
     */
    public function show($id)
    {
        $book = auth()->user()->books()
                      ->with(['categories', 'ratings.reader'])
                      ->withAvg('ratings', 'rating')
                      ->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $book
        ]);
    }

    /**
     * Mettre à jour un de ses livres
     */
    public function update(UpdateBookRequest $request, $id)
    {
        $book = auth()->user()->books()->find($id);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé'
            ], 404);
        }

        $data = [];

        if ($request->has('title')) $data['title'] = $request->title;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('published_date')) $data['published_date'] = $request->published_date;
        if ($request->has('isbn')) $data['isbn'] = $request->isbn;
        if ($request->has('page_count')) $data['page_count'] = $request->page_count;
        if ($request->has('language')) $data['language'] = $request->language;

        // Upload nouveau PDF
        if ($request->hasFile('file_path')) {
            Storage::disk('public')->delete($book->file_path);
            $data['file_path'] = $request->file('file_path')->store('books/pdfs', 'public');
        }

        // Upload nouvelle couverture
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $book->update($data);

        if ($request->has('categories')) {
            $book->categories()->sync($request->categories);
        }

        return response()->json([
            'success' => true,
            'message' => 'Livre mis à jour avec succès',
            'data' => $book->load('categories')
        ]);
    }

    /**
     * Supprimer un de ses livres
     */
    public function destroy($id)
    {
        $book = auth()->user()->books()->find($id);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Livre non trouvé'
            ], 404);
        }

        // Supprimer les fichiers
        Storage::disk('public')->delete($book->file_path);
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->categories()->detach();
        $book->favorites()->detach();
        $book->ratings()->delete();
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Livre supprimé avec succès'
        ]);
    }

    /**
     * Statistiques de l'auteur
     */
    public function stats()
    {
        $author = auth()->user();
        
        $books = $author->books()->withCount('ratings')->get();
        
        $stats = [
            'total_books' => $books->count(),
            'total_views' => $books->sum('views_count'),
            'total_ratings' => $books->sum('ratings_count'),
            'average_rating' => round($books->avg(function($book) {
                return $book->ratings()->avg('rating') ?? 0;
            }), 1),
            'books_by_category' => $author->books()
                                         ->with('categories')
                                         ->get()
                                         ->flatMap->categories
                                         ->groupBy('name')
                                         ->map->count(),
            'most_popular_book' => $books->sortByDesc('views_count')->first()?->title,
            'best_rated_book' => $books->sortByDesc(function($book) {
                return $book->ratings()->avg('rating') ?? 0;
            })->first()?->title,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}