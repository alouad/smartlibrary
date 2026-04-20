<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    protected $table = 'reading_progress';
    
    protected $fillable = [
        'reader_id',
        'book_id',
        'current_page',
        'progress_percent',
        'last_read_at',
        'is_completed',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'is_completed' => 'boolean',
        'progress_percent' => 'decimal:2',
        'current_page' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    /**
     * Relation avec le lecteur
     */
    public function reader()
    {
        return $this->belongsTo(Reader::class, 'reader_id');
    }

    /**
     * Relation avec le livre
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    // =====================================================
    // MÉTHODES UTILITAIRES
    // =====================================================

    /**
     * Mettre à jour la progression
     */
    public function updateProgress($page)
    {
        $this->current_page = $page;
        
        if ($this->book && $this->book->page_count && $this->book->page_count > 0) {
            $this->progress_percent = ($page / $this->book->page_count) * 100;
            
            if ($this->progress_percent >= 100) {
                $this->is_completed = true;
            }
        }
        
        $this->last_read_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Marquer comme terminé
     */
    public function markAsCompleted()
    {
        $this->is_completed = true;
        $this->progress_percent = 100;
        
        if ($this->book && $this->book->page_count) {
            $this->current_page = $this->book->page_count;
        }
        
        $this->last_read_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Vérifier si le livre est terminé
     */
    public function isCompleted()
    {
        return $this->is_completed;
    }

    /**
     * Obtenir la progression pour un lecteur et un livre
     */
    public static function getProgress($readerId, $bookId)
    {
        return self::where('reader_id', $readerId)
                   ->where('book_id', $bookId)
                   ->first();
    }
}