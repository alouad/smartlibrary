<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Favorite extends Pivot
{
    use HasFactory;
    
    protected $table = 'favorites';
    
    public $incrementing = false;

    protected $fillable = [
        'reader_id',
        'book_id',
        'created_at',
    ];
}