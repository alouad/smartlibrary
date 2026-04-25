<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reader;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected $reader;
    protected $readerToken;
    protected $author;
    protected $book;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un lecteur
        $this->reader = Reader::create([
            'name' => 'Test Reader',
            'email' => 'reader@test.com',
            'password' => bcrypt('password123'),
        ]);
        $this->readerToken = $this->reader->createToken('test_token')->plainTextToken;

        // Créer un auteur
        $this->author = Author::create([
            'name' => 'Test Author',
            'email' => 'author@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Créer un livre
        $this->book = Book::create([
            'title' => 'Test Book',
            'author_id' => $this->author->id,
            'file_path' => 'test.pdf',
            'description' => 'Test description',
        ]);
    }

    /**
     * Test qu'un lecteur peut ajouter un livre aux favoris
     */
    public function test_reader_can_add_favorite(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/favorites/' . $this->book->id);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre ajouté aux favoris'
                 ]);

        $this->assertDatabaseHas('favorites', [
            'reader_id' => $this->reader->id,
            'book_id' => $this->book->id,
        ]);
    }

    /**
     * Test qu'un lecteur ne peut pas ajouter deux fois le même favori
     */
    public function test_reader_cannot_add_same_favorite_twice(): void
    {
        // Ajouter une première fois
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/favorites/' . $this->book->id);

        // Ajouter une deuxième fois
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/favorites/' . $this->book->id);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Ce livre est déjà dans vos favoris'
                 ]);
    }

    /**
     * Test qu'un lecteur peut lister ses favoris
     */
    public function test_reader_can_list_favorites(): void
    {
        // Ajouter un favori
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/favorites/' . $this->book->id);

        // Lister les favoris
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->getJson('/api/favorites');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'title']
                         ]
                     ]
                 ]);
        
        $this->assertCount(1, $response->json('data.data'));
    }

    /**
     * Test qu'un lecteur peut retirer un livre des favoris
     */
    public function test_reader_can_remove_favorite(): void
    {
        // Ajouter un favori
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/favorites/' . $this->book->id);

        // Supprimer le favori
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->deleteJson('/api/favorites/' . $this->book->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre retiré des favoris'
                 ]);

        $this->assertDatabaseMissing('favorites', [
            'reader_id' => $this->reader->id,
            'book_id' => $this->book->id,
        ]);
    }

    /**
     * Test qu'un lecteur peut vérifier si un livre est en favori
     */
    public function test_reader_can_check_if_book_is_favorite(): void
    {
        // Vérifier avant ajout
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->getJson('/api/favorites/check/' . $this->book->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'is_favorite' => false
                     ]
                 ]);

        // Ajouter un favori
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/favorites/' . $this->book->id);

        // Vérifier après ajout
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->getJson('/api/favorites/check/' . $this->book->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'is_favorite' => true
                     ]
                 ]);
    }

    /**
     * Test qu'un auteur ne peut pas utiliser les favoris
     */
    public function test_author_cannot_use_favorites(): void
    {
        $author = Author::create([
            'name' => 'Another Author',
            'email' => 'author2@test.com',
            'password' => bcrypt('password123'),
        ]);

        $authorToken = $author->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $authorToken,
        ])->postJson('/api/favorites/' . $this->book->id);

        $response->assertStatus(403);
    }

    /**
     * Test qu'un utilisateur non authentifié ne peut pas ajouter de favori
     */
    public function test_unauthenticated_user_cannot_add_favorite(): void
    {
        $response = $this->postJson('/api/favorites/' . $this->book->id);

        $response->assertStatus(401);
    }

    /**
     * Test l'ajout d'un livre inexistant aux favoris
     */
    public function test_cannot_add_nonexistent_book_to_favorites(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/favorites/99999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Livre non trouvé'
                 ]);
    }
}