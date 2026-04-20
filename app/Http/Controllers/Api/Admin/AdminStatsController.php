<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Rating;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin');
    }

    /**
     * Statistiques générales
     */
    public function index()
    {
        $stats = [
            'total_readers' => Reader::count(),
            'total_authors' => Author::count(),
            'total_books' => Book::count(),
            'total_categories' => Category::count(),
            'total_ratings' => Rating::count(),
            
            // Statistiques récentes (30 derniers jours)
            'new_readers' => Reader::where('created_at', '>=', now()->subDays(30))->count(),
            'new_authors' => Author::where('created_at', '>=', now()->subDays(30))->count(),
            'new_books' => Book::where('created_at', '>=', now()->subDays(30))->count(),
            'new_ratings' => Rating::where('created_at', '>=', now()->subDays(30))->count(),
            
            // Statistiques des livres
            'most_viewed_books' => Book::orderBy('views_count', 'desc')
                                       ->limit(5)
                                       ->get(['id', 'title', 'views_count']),
            
            'best_rated_books' => Book::withAvg('ratings', 'rating')
                                      ->having('ratings_avg_rating', '>', 0)
                                      ->orderBy('ratings_avg_rating', 'desc')
                                      ->limit(5)
                                      ->get(['id', 'title', 'ratings_avg_rating']),
            
            'most_rated_books' => Book::withCount('ratings')
                                      ->orderBy('ratings_count', 'desc')
                                      ->limit(5)
                                      ->get(['id', 'title', 'ratings_count']),
            
            // Statistiques par langue
            'books_by_language' => Book::selectRaw('language, count(*) as total')
                                       ->groupBy('language')
                                       ->get(),
            
            // Répartition des notes
            'ratings_distribution' => [
                '5_etoiles' => Rating::where('rating', 5)->count(),
                '4_etoiles' => Rating::where('rating', 4)->count(),
                '3_etoiles' => Rating::where('rating', 3)->count(),
                '2_etoiles' => Rating::where('rating', 2)->count(),
                '1_etoile' => Rating::where('rating', 1)->count(),
            ],
            
            // Top auteurs
            'top_authors' => Author::withCount('books')
                                   ->orderBy('books_count', 'desc')
                                   ->limit(5)
                                   ->get(['id', 'name', 'books_count']),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Statistiques détaillées avec période
     */
    public function detailed(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $groupBy = match($period) {
            'day' => 'DATE(created_at)',
            'week' => 'YEARWEEK(created_at)',
            'month' => 'DATE_FORMAT(created_at, "%Y-%m")',
            'year' => 'YEAR(created_at)',
            default => 'DATE(created_at)',
        };

        $stats = [
            'period' => $period,
            'readers_growth' => Reader::selectRaw("{$groupBy} as period, count(*) as total")
                                      ->groupBy('period')
                                      ->orderBy('period')
                                      ->get(),
            
            'books_growth' => Book::selectRaw("{$groupBy} as period, count(*) as total")
                                  ->groupBy('period')
                                  ->orderBy('period')
                                  ->get(),
            
            'ratings_evolution' => Rating::selectRaw("{$groupBy} as period, avg(rating) as average, count(*) as total")
                                         ->groupBy('period')
                                         ->orderBy('period')
                                         ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}