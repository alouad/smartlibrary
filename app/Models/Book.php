<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'description',
        'cover_image',
        'file_path',
        'is_premium',
        'published_date',
        'isbn',
        'page_count',
        'language',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'published_date' => 'date',
            'page_count' => 'integer',
            'views_count' => 'integer',
            'is_premium' => 'boolean',
        ];
    }

    /**
     * Get the author that wrote the book.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * The categories that belong to the book.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'book_id', 'category_id');
    }

    /**
     * The readers who favorited this book.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(Reader::class, 'favorites', 'book_id', 'reader_id')->withTimestamps();
    }

    /**
     * The ratings for this book.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    
    /**
     * Calculate the average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?: 0;
    }
}