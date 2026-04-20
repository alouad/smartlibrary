<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = Author::all();
        $categories = Category::all();

        // Livres classiques
        $books = [
            [
                'title' => 'Les Misérables',
                'author' => 'Victor Hugo',
                'description' => "Les Misérables est un roman de Victor Hugo paru en 1862. Il a donné lieu à de nombreuses adaptations, au cinéma et sur d'autres supports. L'histoire se déroule en France au XIXe siècle et suit plusieurs personnages, dont Jean Valjean, un ancien forçat en quête de rédemption.",
                'published_date' => '1862-01-01',
                'isbn' => '978-2253004226',
                'page_count' => 1584,
                'language' => 'français',
                'categories' => ['Roman', 'Histoire'],
            ],
            [
                'title' => 'Notre-Dame de Paris',
                'author' => 'Victor Hugo',
                'description' => "Notre-Dame de Paris est un roman historique de Victor Hugo, publié en 1831. L'histoire se déroule à Paris au XVe siècle et met en scène Quasimodo, le sonneur de la cathédrale, et Esmeralda, une bohémienne.",
                'published_date' => '1831-01-01',
                'isbn' => '978-2253004721',
                'page_count' => 702,
                'language' => 'français',
                'categories' => ['Roman', 'Histoire'],
            ],
            [
                'title' => "L'Étranger",
                'author' => 'Albert Camus',
                'description' => "L'Étranger est le premier roman d'Albert Camus, paru en 1942. Il met en scène un personnage-narrateur, Meursault, qui semble indifférent à tout ce qui l'entoure jusqu'au meurtre qu'il commet.",
                'published_date' => '1942-01-01',
                'isbn' => '978-2070360024',
                'page_count' => 172,
                'language' => 'français',
                'categories' => ['Roman', 'Philosophie'],
            ],
            [
                'title' => 'La Peste',
                'author' => 'Albert Camus',
                'description' => "La Peste est un roman d'Albert Camus publié en 1947. Il raconte la vie quotidienne des habitants d'Oran pendant une épidémie de peste.",
                'published_date' => '1947-01-01',
                'isbn' => '978-2070360420',
                'page_count' => 336,
                'language' => 'français',
                'categories' => ['Roman', 'Philosophie'],
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'description' => "1984 est un roman d'anticipation dystopique de George Orwell, publié en 1949. Il décrit une société totalitaire où le Parti contrôle tous les aspects de la vie des citoyens.",
                'published_date' => '1949-01-01',
                'isbn' => '978-2070368228',
                'page_count' => 416,
                'language' => 'français',
                'categories' => ['Science-Fiction', 'Philosophie'],
            ],
            [
                'title' => 'La Ferme des animaux',
                'author' => 'George Orwell',
                'description' => "La Ferme des animaux est un roman de George Orwell publié en 1945. C'est une satire du totalitarisme où les animaux d'une ferme se révoltent contre leur fermier.",
                'published_date' => '1945-01-01',
                'isbn' => '978-2070360161',
                'page_count' => 144,
                'language' => 'français',
                'categories' => ['Roman', 'Philosophie'],
            ],
            [
                'title' => 'Orgueil et Préjugés',
                'author' => 'Jane Austen',
                'description' => "Orgueil et Préjugés est un roman de Jane Austen publié en 1813. Il raconte l'histoire d'Elizabeth Bennet et de sa relation avec le riche Mr Darcy.",
                'published_date' => '1813-01-01',
                'isbn' => '978-2070360185',
                'page_count' => 432,
                'language' => 'français',
                'categories' => ['Roman'],
            ],
            [
                'title' => 'La Métamorphose',
                'author' => 'Franz Kafka',
                'description' => "La Métamorphose est un roman de Franz Kafka publié en 1915. Il raconte l'histoire de Gregor Samsa qui se réveille un matin transformé en insecte.",
                'published_date' => '1915-01-01',
                'isbn' => '978-2070360246',
                'page_count' => 128,
                'language' => 'français',
                'categories' => ['Roman', 'Philosophie'],
            ],
            [
                'title' => 'Cent ans de solitude',
                'author' => 'Gabriel García Márquez',
                'description' => "Cent ans de solitude est un roman de Gabriel García Márquez publié en 1967. Il raconte l'histoire de la famille Buendía sur sept générations.",
                'published_date' => '1967-01-01',
                'isbn' => '978-2070360161',
                'page_count' => 480,
                'language' => 'français',
                'categories' => ['Roman', 'Fantasy'],
            ],
            [
                'title' => 'Du côté de chez Swann',
                'author' => 'Marcel Proust',
                'description' => "Du côté de chez Swann est le premier volume de 'À la recherche du temps perdu' de Marcel Proust, publié en 1913.",
                'published_date' => '1913-01-01',
                'isbn' => '978-2070360161',
                'page_count' => 528,
                'language' => 'français',
                'categories' => ['Roman'],
            ],
        ];

        foreach ($books as $bookData) {
            // Trouver l'auteur
            $author = Author::where('name', $bookData['author'])->first();
            
            if ($author) {
                $categoriesForBook = $bookData['categories'];
                unset($bookData['author']);
                unset($bookData['categories']);
                
                $book = Book::create([
                    'title' => $bookData['title'],
                    'author_id' => $author->id,
                    'description' => $bookData['description'],
                    'cover_image' => null,
                    'file_path' => 'books/pdfs/' . Str::slug($bookData['title']) . '.pdf',
                    'published_date' => $bookData['published_date'],
                    'isbn' => $bookData['isbn'],
                    'page_count' => $bookData['page_count'],
                    'language' => $bookData['language'],
                    'views_count' => rand(100, 5000),
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now(),
                ]);
                
                // Attacher les catégories
                $categoryIds = Category::whereIn('name', $categoriesForBook)->pluck('id');
                $book->categories()->attach($categoryIds);
            }
        }

        // Créer 80 livres supplémentaires avec des données aléatoires
        Book::factory()
            ->count(80)
            ->make()
            ->each(function ($book) use ($authors, $categories) {
                $book->author_id = $authors->random()->id;
                $book->created_at = now()->subDays(rand(1, 365));
                $book->save();
                
                // Attacher 1 à 3 catégories aléatoires
                $book->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')->toArray()
                );
            });
    }
}