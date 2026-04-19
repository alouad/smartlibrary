<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\Reader;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $readers = Reader::all();
        $books = Book::all();

        $reviews = [
            "Excellent livre, je l'ai dévoré en une soirée !",
            "Très bon ouvrage, je recommande vivement.",
            "Intéressant mais un peu long par moments.",
            "Un classique incontournable, à lire absolument.",
            "L'auteur nous transporte dans son univers captivant.",
            "Déçu, je m'attendais à mieux compte tenu des critiques.",
            "Superbe écriture, personnages attachants.",
            "À lire absolument, une pépite littéraire !",
            "Un peu difficile au début mais captivant ensuite.",
            "Magnifique histoire, j'ai adoré chaque page.",
            "Très bien documenté, j'ai appris beaucoup de choses.",
            "Style d'écriture fluide et agréable.",
            "Un livre qui fait réfléchir, je le recommande.",
            "Pas vraiment mon style, mais bien écrit.",
            "Un chef-d'œuvre de la littérature.",
            "Histoire prenante, personnages profonds.",
            "Quelques longueurs mais dans l'ensemble très bon.",
            "Une lecture rafraîchissante et originale.",
            "J'ai été transporté du début à la fin.",
            "Un livre qui mérite d'être connu et partagé.",
        ];

        $reviewsFr = [
            "Excellent livre, je l'ai dévoré en une soirée !",
            "Très bon ouvrage, je recommande vivement.",
            "Intéressant mais un peu long par moments.",
            "Un classique incontournable, à lire absolument.",
            "L'auteur nous transporte dans son univers captivant.",
            "Déçu, je m'attendais à mieux compte tenu des critiques.",
            "Superbe écriture, personnages attachants.",
            "À lire absolument, une pépite littéraire !",
            "Un peu difficile au début mais captivant ensuite.",
            "Magnifique histoire, j'ai adoré chaque page.",
        ];

        // Pour chaque livre, créer 5 à 30 évaluations
        foreach ($books as $book) {
            $numberOfRatings = rand(5, 30);
            $selectedReaders = $readers->random(min($numberOfRatings, $readers->count()));
            
            $ratingsCount = 0;
            
            foreach ($selectedReaders as $reader) {
                // Vérifier si le lecteur n'a pas déjà évalué ce livre
                $exists = Rating::where('reader_id', $reader->id)
                                ->where('book_id', $book->id)
                                ->exists();
                
                if (!$exists && $ratingsCount < $numberOfRatings) {
                    // Générer une note avec une distribution réaliste
                    $rand = rand(1, 100);
                    if ($rand <= 5) {
                        $rating = 1; // 5% de notes 1
                    } elseif ($rand <= 15) {
                        $rating = 2; // 10% de notes 2
                    } elseif ($rand <= 35) {
                        $rating = 3; // 20% de notes 3
                    } elseif ($rand <= 65) {
                        $rating = 4; // 30% de notes 4
                    } else {
                        $rating = 5; // 35% de notes 5
                    }
                    
                    // 70% de chance d'avoir un commentaire
                    $hasReview = rand(1, 100) <= 70;
                    
                    Rating::create([
                        'reader_id' => $reader->id,
                        'book_id' => $book->id,
                        'rating' => $rating,
                        'review' => $hasReview ? $reviewsFr[array_rand($reviewsFr)] : null,
                        'created_at' => now()->subDays(rand(1, 180)),
                        'updated_at' => now(),
                    ]);
                    
                    $ratingsCount++;
                }
            }
        }
    }
}