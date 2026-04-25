<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        $totalReaders = Reader::count();
        $totalAuthors = Author::count();
        $totalUsers = $totalReaders + $totalAuthors + User::count();
        $totalBooks = Book::count();
        $totalCategories = Category::count();
        $totalRatings = Rating::count();

        $topBooks = Book::withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->take(5)
            ->get();

        $topAuthors = Author::withCount('books')
            ->orderByDesc('books_count')
            ->take(5)
            ->get();

        return response()->json([
            'total_users' => $totalUsers,
            'total_readers' => $totalReaders,
            'total_authors' => $totalAuthors,
            'total_books' => $totalBooks,
            'total_categories' => $totalCategories,
            'total_ratings' => $totalRatings,
            'top_books' => $topBooks,
            'top_authors' => $topAuthors
        ]);
    }

    public function users()
    {
        $readers = Reader::all();
        $authors = Author::all();

        return response()->json([
            'readers' => $readers,
            'authors' => $authors
        ]);
    }
}
