<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $adminToken;
    protected $reader;
    protected $readerToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un admin (lecteur avec is_admin = true)
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
     * Test la liste des catégories (public)
     */
    public function test_can_list_categories(): void
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'slug']
                     ]
                 ]);
        
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test l'affichage d'une catégorie (public)
     */
    public function test_can_show_category(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
        ]);

        $response = $this->getJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $category->id,
                         'name' => 'Test Category'
                     ]
                 ]);
    }

    /**
     * Test la création d'une catégorie par l'admin
     */
    public function test_admin_can_create_category(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/categories', [
            'name' => 'New Category',
            'description' => 'Category description',
            'color' => '#3498db',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Catégorie créée avec succès',
                     'data' => [
                         'name' => 'New Category'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category'
        ]);
    }

    /**
     * Test qu'un lecteur normal ne peut pas créer de catégorie
     */
    public function test_reader_cannot_create_category(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test la mise à jour d'une catégorie par l'admin
     */
    public function test_admin_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Old Name',
            'slug' => 'old-name'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson('/api/categories/' . $category->id, [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated Name'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name'
        ]);
    }

    /**
     * Test la suppression d'une catégorie par l'admin
     */
    public function test_admin_can_delete_category(): void
    {
        $category = Category::create([
            'name' => 'To Delete',
            'slug' => 'to-delete'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Catégorie supprimée avec succès'
                 ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test la récupération des livres d'une catégorie
     */
    public function test_can_get_books_by_category(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $response = $this->getJson('/api/categories/' . $category->id . '/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'category',
                         'books'
                     ]
                 ]);
    }

    /**
     * Test qu'une catégorie inexistante retourne 404
     */
    public function test_show_nonexistent_category_returns_404(): void
    {
        $response = $this->getJson('/api/categories/99999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Catégorie non trouvée'
                 ]);
    }
}