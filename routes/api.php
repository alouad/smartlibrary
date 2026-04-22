<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\BookController as PremiumBookController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/search', [BookController::class, 'search']);
Route::get('/books/{id}', [BookController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // Premium / Reader Books Access
    Route::get('/books/{book}/download', [PremiumBookController::class, 'downloadPdf']);
    Route::get('/books/{book}/read', [PremiumBookController::class, 'readOnline']);
    
    // Reader Routes
    Route::middleware('reader')->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/books/{book}/favorite', [FavoriteController::class, 'add']);
        Route::delete('/books/{book}/favorite', [FavoriteController::class, 'remove']);
        
        Route::post('/ratings', [RatingController::class, 'store']);
        Route::put('/ratings/{rating}', [RatingController::class, 'update']);
        Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);
    });
    
    // Author Routes
    Route::middleware('author')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::post('/books/{book}', [BookController::class, 'update']); // Using POST with _method=PUT constraint for form-data
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });
    
    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/users', [AdminController::class, 'users']);
        
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        
        // Admin has delete rights on books and ratings via Policies naturally, but handled in their respective controllers.
    });
});