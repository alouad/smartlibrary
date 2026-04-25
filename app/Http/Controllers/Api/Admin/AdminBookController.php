<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin');
    }

    /**
     * Liste tous les livres
     */
    public function index(Request $request)
    {
        $query = Book::with(['author', 'categories'])
                     ->withAvg('ratings', 'rating')
                     ->withCount('ratings');

        if ($request->has('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('author')) {
            $query->where('author_id', $request->author);
        }

        $books = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * Afficher un livre spécifique
     */
    public function show($id)
    {
        $book = Book::with(['author', 'categories', 'ratings.reader'])
                    ->withAvg('ratings', 'rating')
                    ->withCount('ratings')
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
     * Créer un nouveau livre
     */
    public function store(StoreBookRequest $request)
    {
        // Vérifier que l'auteur existe
        $author = Author::find($request->author_id);
        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Auteur non trouvé'
            ], 404);
        }

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
            'author_id' => $request->author_id,
            'description' => $request->description,
            'cover_image' => $coverPath,
            'file_path' => $pdfPath,
            'published_date' => $request->published_date,
            'isbn' => $request->isbn,
            'page_count' => $request->page_count,
            'language' => $request->language ?? 'français',
        ]);

        if ($request->has('categories')) {
            $book->categories()->attach($request->categories);
        }

        return response()->json([
            'success' => true,
            'message' => 'Livre créé avec succès',
            'data' => $book->load(['author', 'categories'])
        ], 201);
    }

    /**
     * Met à jour un livre
     */
    public function update(UpdateBookRequest $request, $id)
    {
        $book = Book::find($id);
        
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
        
        if ($request->has('author_id')) {
            $author = Author::find($request->author_id);
            if (!$author) {
                return response()->json([
                    'success' => false,
                    'message' => 'Auteur non trouvé'
                ], 404);
            }
            $data['author_id'] = $request->author_id;
        }

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
            'data' => $book->load(['author', 'categories'])
        ]);
    }

    /**
     * Supprime un livre
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        
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

        // Supprimer les relations
        $book->categories()->detach();
        $book->favorites()->detach();
        $book->ratings()->delete();
        
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Livre supprimé avec succès'
        ]);
    }
}