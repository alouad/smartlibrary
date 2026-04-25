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

class BookTest extends TestCase
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
     * Test la liste des livres
     */
    public function test_can_list_books(): void
    {
        Book::factory()->count(5)->create(['author_id' => $this->author->id]);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'title', 'author_id']
                         ]
                     ]
                 ]);
    }

    /**
     * Test la création d'un livre par un auteur
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
            'title' => 'Test Book',
            'description' => 'This is a test book description',
            'file_path' => $pdfFile,
            'cover_image' => $coverFile,
            'page_count' => 200,
            'language' => 'français',
            'categories' => [$category->id],
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre créé avec succès'
                 ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author_id' => $this->author->id,
        ]);

        // Vérifier que les fichiers ont été uploadés
        Storage::disk('public')->assertExists($response->json('data.file_path'));
    }

    /**
     * Test qu'un lecteur ne peut pas créer de livre
     */
    public function test_reader_cannot_create_book(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/author/books', [
            'title' => 'Test Book',
            'file_path' => UploadedFile::fake()->create('book.pdf', 1024),
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test l'affichage d'un livre
     */
    public function test_can_show_book(): void
    {
        $book = Book::factory()->create(['author_id' => $this->author->id]);

        $response = $this->getJson('/api/books/' . $book->id);

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
     * Test la mise à jour d'un livre par son auteur
     */
    public function test_author_can_update_own_book(): void
    {
        $book = Book::factory()->create(['author_id' => $this->author->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->putJson('/api/author/books/' . $book->id, [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre mis à jour avec succès'
                 ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title'
        ]);
    }

    /**
     * Test qu'un auteur ne peut pas modifier le livre d'un autre auteur
     */
    public function test_author_cannot_update_other_author_book(): void
    {
        $otherAuthor = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $otherAuthor->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authorToken,
        ])->putJson('/api/author/books/' . $book->id, [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test la suppression d'un livre par son auteur
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
     * Test la recherche de livres
     */
    public function test_can_search_books(): void
    {
        Book::factory()->create([
            'title' => 'Unique Special Title',
            'author_id' => $this->author->id
        ]);

        Book::factory()->create([
            'title' => 'Another Book',
            'author_id' => $this->author->id
        ]);

        $response = $this->getJson('/api/books/search?title=Unique');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);
        
        $this->assertCount(1, $response->json('data.data'));
    }

    /**
     * Test le filtrage des livres par catégorie
     */
    public function test_can_filter_books_by_category(): void
    {
        $category = Category::create([
            'name' => 'Science-Fiction',
            'slug' => 'science-fiction'
        ]);

        $book1 = Book::factory()->create(['author_id' => $this->author->id]);
        $book2 = Book::factory()->create(['author_id' => $this->author->id]);
        
        $book1->categories()->attach($category->id);

        $response = $this->getJson('/api/books?category=' . $category->id);

        $response->assertStatus(200);
        
        // Vérifier que seul le livre avec la catégorie apparaît
        $bookIds = collect($response->json('data.data'))->pluck('id');
        $this->assertContains($book1->id, $bookIds);
        $this->assertNotContains($book2->id, $bookIds);
    }
}