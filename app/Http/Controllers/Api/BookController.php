<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Requests\SearchBookRequest;
use App\Http\Resources\BookResource;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $books = Book::with(['author', 'categories'])->paginate(15);
        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['author_id'] = $request->user()->id;

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $this->fileUploadService->upload($request->file('cover_image'), 'covers');
            }

            if ($request->hasFile('file')) {
                $data['file_path'] = $this->fileUploadService->upload($request->file('file'), 'books');
            }

            $book = Book::create($data);

            if (isset($data['categories'])) {
                $book->categories()->sync($data['categories']);
            }

            return new BookResource($book->load(['author', 'categories']));
        });
    }

    public function show($id)
    {
        $book = Book::with(['author', 'categories'])->findOrFail($id);
        $book->increment('views_count');
        
        return new BookResource($book);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        return DB::transaction(function () use ($request, $book) {
            $data = $request->validated();

            if ($request->hasFile('cover_image')) {
                $this->fileUploadService->delete($book->cover_image);
                $data['cover_image'] = $this->fileUploadService->upload($request->file('cover_image'), 'covers');
            }

            if ($request->hasFile('file')) {
                $this->fileUploadService->delete($book->file_path);
                $data['file_path'] = $this->fileUploadService->upload($request->file('file'), 'books');
            }

            $book->update($data);

            if (isset($data['categories'])) {
                $book->categories()->sync($data['categories']);
            }

            return new BookResource($book->load(['author', 'categories']));
        });
    }

    public function destroy(Book $book, Request $request)
    {
        if ($request->user()->cannot('delete', $book)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->fileUploadService->delete($book->cover_image);
        $this->fileUploadService->delete($book->file_path);
        
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }

    public function search(SearchBookRequest $request)
    {
        $query = Book::with(['author', 'categories']);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhereHas('author', function ($authorQ) use ($term) {
                      $authorQ->where('name', 'like', $term);
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        if ($request->filled('min_rating')) {
            $query->whereHas('ratings', function ($q) {
            }, '>=', 1)->withAvg('ratings', 'rating')
                  ->having('ratings_avg_rating', '>=', $request->min_rating);
        }

        $sortBy = $request->sort_by ?? 'created_at';
        $order = $request->order ?? 'desc';
        
        if ($sortBy === 'date') $sortBy = 'published_date';
        elseif ($sortBy === 'popularity') $sortBy = 'views_count';
        
        $query->orderBy($sortBy, $order);

        $books = $query->paginate(15);
        return BookResource::collection($books);
    }
}