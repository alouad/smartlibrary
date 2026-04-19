<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'reader_id',
        'book_id',
        'rating',
        'review',
    ];

    /**
     * Get the reader who gave the rating.
     */
    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    /**
     * Get the book that was rated.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}