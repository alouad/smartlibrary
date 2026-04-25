<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reader;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $adminToken;
    protected $reader;
    protected $readerToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un admin
        $this->admin = Reader::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);
        $this->adminToken = $this->admin->createToken('test_token')->plainTextToken;

        // Créer un lecteur normal
        $this->reader = Reader::create([
            'name' => 'Normal Reader',
            'email' => 'reader@test.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);
        $this->readerToken = $this->reader->createToken('test_token')->plainTextToken;
    }

    /**
     * Test que l'admin peut voir les statistiques
     */
    public function test_admin_can_view_stats(): void
    {
        // Créer des données
        Reader::factory()->count(5)->create();
        Author::factory()->count(3)->create();
        Book::factory()->count(10)->create();
        Category::factory()->count(4)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/admin/stats');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_readers',
                         'total_authors',
                         'total_books',
                         'total_categories',
                         'total_ratings',
                         'new_readers',
                         'new_authors',
                         'new_books',
                         'most_viewed_books',
                         'best_rated_books',
                         'most_rated_books',
                         'books_by_language',
                         'ratings_distribution',
                         'top_authors'
                     ]
                 ]);
    }

    /**
     * Test que l'admin peut lister tous les utilisateurs
     */
    public function test_admin_can_list_all_users(): void
    {
        // Créer des utilisateurs supplémentaires
        Reader::factory()->count(3)->create();
        Author::factory()->count(2)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/admin/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'email', 'user_type']
                     ]
                 ]);
    }

    /**
     * Test que l'admin peut voir le détail d'un utilisateur
     */
    public function test_admin_can_view_user_detail(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/admin/users/' . $this->reader->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $this->reader->id,
                         'name' => 'Normal Reader',
                         'email' => 'reader@test.com'
                     ]
                 ]);
    }

    /**
     * Test que l'admin peut modifier un utilisateur
     */
    public function test_admin_can_update_user(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson('/api/admin/users/' . $this->reader->id, [
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Utilisateur mis à jour avec succès',
                     'data' => [
                         'name' => 'Updated Name',
                         'email' => 'updated@test.com'
                     ]
                 ]);

        $this->assertDatabaseHas('readers', [
            'id' => $this->reader->id,
            'name' => 'Updated Name',
            'email' => 'updated@test.com'
        ]);
    }

    /**
     * Test que l'admin peut supprimer un utilisateur
     */
    public function test_admin_can_delete_user(): void
    {
        $userToDelete = Reader::create([
            'name' => 'To Delete',
            'email' => 'delete@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson('/api/admin/users/' . $userToDelete->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Utilisateur supprimé avec succès'
                 ]);

        $this->assertDatabaseMissing('readers', ['id' => $userToDelete->id]);
    }

    /**
     * Test que l'admin peut lister tous les livres
     */
    public function test_admin_can_list_all_books(): void
    {
        Book::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/admin/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'title', 'author']
                         ]
                     ]
                 ]);
    }

    /**
     * Test que l'admin peut créer un livre pour n'importe quel auteur
     */
    public function test_admin_can_create_book_for_any_author(): void
    {
        $author = Author::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/admin/books', [
            'title' => 'Admin Created Book',
            'author_id' => $author->id,
            'file_path' => 'test.pdf',
            'description' => 'Test description',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'title' => 'Admin Created Book',
                         'author_id' => $author->id
                     ]
                 ]);
    }

    /**
     * Test que l'admin peut modifier n'importe quel livre
     */
    public function test_admin_can_update_any_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson('/api/admin/books/' . $book->id, [
            'title' => 'Updated by Admin',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'title' => 'Updated by Admin'
                     ]
                 ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated by Admin'
        ]);
    }

    /**
     * Test que l'admin peut supprimer n'importe quel livre
     */
    public function test_admin_can_delete_any_book(): void
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create(['author_id' => $author->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson('/api/admin/books/' . $book->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Livre supprimé avec succès'
                 ]);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /**
     * Test qu'un lecteur normal ne peut pas accéder aux routes admin
     */
    public function test_reader_cannot_access_admin_routes(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->getJson('/api/admin/stats');

        $response->assertStatus(403);
    }

    /**
     * Test qu'un auteur ne peut pas accéder aux routes admin
     */
    public function test_author_cannot_access_admin_routes(): void
    {
        $author = Author::create([
            'name' => 'Author',
            'email' => 'author@test.com',
            'password' => bcrypt('password123'),
        ]);
        $authorToken = $author->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $authorToken,
        ])->getJson('/api/admin/stats');

        $response->assertStatus(403);
    }
}