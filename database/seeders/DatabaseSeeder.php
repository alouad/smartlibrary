<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Author;
use App\Models\Reader;
use App\Models\Book;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrateurs (2)
        User::create([
            'name' => 'Admin One',
            'email' => 'admin1@smartlibrary.com',
            'password' => Hash::make('password'),
        ]);
        User::create([
            'name' => 'Admin Two',
            'email' => 'admin2@smartlibrary.com',
            'password' => Hash::make('password'),
        ]);

        // Auteurs (5)
        for ($i = 1; $i <= 5; $i++) {
            Author::create([
                'name' => "Author $i",
                'email' => "author$i@smartlibrary.com",
                'password' => Hash::make('password'),
                'bio' => "Bio de l'auteur $i",
            ]);
        }

        // Lecteurs (10)
        for ($i = 1; $i <= 10; $i++) {
            Reader::create([
                'name' => "Reader $i",
                'email' => "reader$i@smartlibrary.com",
                'password' => Hash::make('password'),
            ]);
        }

        // Catégories (5)
        $categories = ['Science Fiction', 'Fantasy', 'Roman', 'Technologie', 'Histoire'];
        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => \Illuminate\Support\Str::slug($cat),
                'description' => "Livres dans la catégorie $cat",
                'color' => '#000000'
            ]);
        }

        // Livres (20)
        for ($i = 1; $i <= 20; $i++) {
            $book = Book::create([
                'title' => "Book Title $i",
                'author_id' => rand(1, 5),
                'description' => "Description pour le livre $i",
                'published_date' => now()->subDays(rand(1, 1000)),
                'isbn' => "978-" . rand(1000000000, 9999999999),
                'page_count' => rand(100, 500),
                'language' => 'Français',
                'file_path' => "books/dummy$i.pdf",
            ]);

            // Attacher 1 ou 2 catégories
            $book->categories()->attach([rand(1, 3), rand(4, 5)]);
        }
    }
}