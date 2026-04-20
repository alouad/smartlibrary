<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Favorite;
use App\Models\Reader;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $readers = Reader::all();
        $books = Book::all();

        // Pour chaque lecteur, ajouter 2 à 15 livres en favoris
        foreach ($readers as $reader) {
            $numberOfFavorites = rand(2, 15);
            $selectedBooks = $books->random(min($numberOfFavorites, $books->count()));
            
            foreach ($selectedBooks as $book) {
                // Vérifier si le favori n'existe pas déjà
                $exists = Favorite::where('reader_id', $reader->id)
                                  ->where('book_id', $book->id)
                                  ->exists();
                
                if (!$exists) {
                    Favorite::create([
                        'reader_id' => $reader->id,
                        'book_id' => $book->id,
                        'created_at' => now()->subDays(rand(1, 180)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}