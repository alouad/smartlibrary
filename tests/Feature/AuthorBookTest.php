<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AuthorBookTest extends TestCase
{
    use RefreshDatabase;

    protected $author;
    protected $authorToken;
    protected $reader;
    protected $readerToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un auteur
        $this->author = Author::create([
            'name' => 'Test Author',
            'email' => 'author@test.com',
            'password' => bcrypt('password123'),
            'bio' => 'Test bio',
        ]);
        $this->authorToken = $this->author->createToken('test_token')->plainTextToken;

        // Créer un lecteur
        $this->reader = Reader::create([
            'name' => 'Test Reader',
            'email' => 'reader@test.com',
            'password' => bcrypt('password123'),
        ]);
        $this->readerToken = $this->reader->createToken('test_token')->plainTextToken;
    }

    /**
     * Test qu'un auteur peut voir ses livres
     */
    public function test_author_can_view_own_books(): void
    {
        Book::factory()->count(3)->create(['author_id' => $this->author->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->getJson('/api/author/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'title', 'author_id']
                         ]
                     ]
                 ]);
        
        $this->assertCount(3, $response->json('data.data'));
    }

    /**
     * Test qu'un auteur peut créer un livre
     */
    public function test_author_can_create_book(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $pdfFile = UploadedFile::fake()->create('book.pdf', 1024);
        $coverFile = UploadedFile::fake()->image('cover.jpg');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->postJson('/api/author/books', [
            'title' => 'My New Book',
            'description' => 'This is my new book description',
            'file_path' => $pdfFile,
            'cover_image' => $coverFile,
            'page_count' => 300,
            'language' => 'français',
            'categories' => [$category->id],
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre créé avec succès',
                     'data' => [
                         'title' => 'My New Book'
                     ]
                 ]);

        $this->assertDatabaseHas('books', [
            'title' => 'My New Book',
            'author_id' => $this->author->id,
        ]);
    }

    /**
     * Test qu'un auteur peut voir le détail d'un de ses livres
     */
    public function test_author_can_view_own_book_detail(): void
    {
        $book = Book::factory()->create(['author_id' => $this->author->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->getJson('/api/author/books/' . $book->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $book->id,
                         'title' => $book->title
                     ]
                 ]);
    }

    /**
     * Test qu'un auteur ne peut pas voir le livre d'un autre auteur
     */
    public function test_author_cannot_view_other_author_book(): void
    {
        $otherAuthor = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $otherAuthor->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->getJson('/api/author/books/' . $book->id);

        $response->assertStatus(404);
    }

    /**
     * Test qu'un auteur peut modifier un de ses livres
     */
    public function test_author_can_update_own_book(): void
    {
        $book = Book::factory()->create(['author_id' => $this->author->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->putJson('/api/author/books/' . $book->id, [
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre mis à jour avec succès',
                     'data' => [
                         'title' => 'Updated Title'
                     ]
                 ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
            'description' => 'Updated description'
        ]);
    }

    /**
     * Test qu'un auteur peut supprimer un de ses livres
     */
    public function test_author_can_delete_own_book(): void
    {
        $book = Book::factory()->create(['author_id' => $this->author->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->deleteJson('/api/author/books/' . $book->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre supprimé avec succès'
                 ]);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /**
     * Test qu'un auteur peut voir ses statistiques
     */
    public function test_author_can_view_stats(): void
    {
        // Créer plusieurs livres
        Book::factory()->count(3)->create([
            'author_id' => $this->author->id,
            'views_count' => 100
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->getJson('/api/author/stats');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_books',
                         'total_views',
                         'total_ratings',
                         'average_rating',
                         'books_by_category',
                         'most_popular_book',
                         'best_rated_book'
                     ]
                 ]);
        
        $this->assertEquals(3, $response->json('data.total_books'));
        $this->assertEquals(300, $response->json('data.total_views'));
    }

    /**
     * Test qu'un lecteur ne peut pas accéder aux routes auteur
     */
    public function test_reader_cannot_access_author_routes(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->getJson('/api/author/books');

        $response->assertStatus(403);
    }

    /**
     * Test qu'un utilisateur non authentifié ne peut pas accéder aux routes auteur
     */
    public function test_unauthenticated_user_cannot_access_author_routes(): void
    {
        $response = $this->getJson('/api/author/books');

        $response->assertStatus(401);
    }
}