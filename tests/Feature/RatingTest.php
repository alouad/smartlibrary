<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reader;
use App\Models\Author;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RatingTest extends TestCase
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
            'page_count' => 200,
        ]);
    }

    /**
     * Test qu'un lecteur peut ajouter une évaluation
     */
    public function test_reader_can_add_rating(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/ratings', [
            'book_id' => $this->book->id,
            'rating' => 5,
            'review' => 'Excellent livre !',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Évaluation ajoutée',
                     'data' => [
                         'rating' => 5,
                         'review' => 'Excellent livre !'
                     ]
                 ]);

        $this->assertDatabaseHas('ratings', [
            'reader_id' => $this->reader->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'review' => 'Excellent livre !'
        ]);
    }

    /**
     * Test qu'un lecteur peut modifier son évaluation
     */
    public function test_reader_can_update_own_rating(): void
    {
        // Créer une évaluation
        $rating = Rating::create([
            'reader_id' => $this->reader->id,
            'book_id' => $this->book->id,
            'rating' => 3,
            'review' => 'Moyen',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->putJson('/api/ratings/' . $rating->id, [
            'rating' => 5,
            'review' => 'Finalement excellent !',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Évaluation mise à jour',
                     'data' => [
                         'rating' => 5,
                         'review' => 'Finalement excellent !'
                     ]
                 ]);

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id,
            'rating' => 5,
            'review' => 'Finalement excellent !'
        ]);
    }

    /**
     * Test qu'un lecteur peut supprimer son évaluation
     */
    public function test_reader_can_delete_own_rating(): void
    {
        // Créer une évaluation
        $rating = Rating::create([
            'reader_id' => $this->reader->id,
            'book_id' => $this->book->id,
            'rating' => 4,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->deleteJson('/api/ratings/' . $rating->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Évaluation supprimée'
                 ]);

        $this->assertDatabaseMissing('ratings', ['id' => $rating->id]);
    }

    /**
     * Test qu'un lecteur ne peut pas modifier l'évaluation d'un autre lecteur
     */
    public function test_reader_cannot_update_other_reader_rating(): void
    {
        // Créer un autre lecteur
        $otherReader = Reader::create([
            'name' => 'Other Reader',
            'email' => 'other@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Créer une évaluation par l'autre lecteur
        $rating = Rating::create([
            'reader_id' => $otherReader->id,
            'book_id' => $this->book->id,
            'rating' => 4,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->putJson('/api/ratings/' . $rating->id, [
            'rating' => 5,
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test la récupération des évaluations d'un livre (public)
     */
    public function test_can_get_book_ratings(): void
    {
        // Créer plusieurs évaluations
        Rating::create([
            'reader_id' => $this->reader->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'review' => 'Super!',
        ]);

        $otherReader = Reader::create([
            'name' => 'Other',
            'email' => 'other2@test.com',
            'password' => bcrypt('password123'),
        ]);

        Rating::create([
            'reader_id' => $otherReader->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review' => 'Très bien',
        ]);

        $response = $this->getJson('/api/books/' . $this->book->id . '/ratings');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'book',
                         'average',
                         'total',
                         'distribution',
                         'ratings'
                     ]
                 ]);
        
        $this->assertEquals(4.5, $response->json('data.average'));
        $this->assertEquals(2, $response->json('data.total'));
    }

    /**
     * Test la validation de la note (doit être entre 1 et 5)
     */
    public function test_rating_must_be_between_1_and_5(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/ratings', [
            'book_id' => $this->book->id,
            'rating' => 6,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['rating']);
    }

    /**
     * Test qu'un lecteur ne peut pas évaluer deux fois le même livre
     */
    public function test_reader_cannot_rate_same_book_twice(): void
    {
        // Première évaluation
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/ratings', [
            'book_id' => $this->book->id,
            'rating' => 4,
        ]);

        // Deuxième évaluation (devrait mettre à jour l'ancienne)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->readerToken,
        ])->postJson('/api/ratings', [
            'book_id' => $this->book->id,
            'rating' => 5,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Évaluation mise à jour'
                 ]);

        // Vérifier qu'il n'y a qu'une seule évaluation
        $this->assertEquals(1, Rating::where('reader_id', $this->reader->id)
                                       ->where('book_id', $this->book->id)
                                       ->count());
    }

    /**
     * Test qu'un auteur ne peut pas évaluer
     */
    public function test_author_cannot_rate_books(): void
    {
        $author = Author::create([
            'name' => 'Author',
            'email' => 'author2@test.com',
            'password' => bcrypt('password123'),
        ]);

        $authorToken = $author->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $authorToken,
        ])->postJson('/api/ratings', [
            'book_id' => $this->book->id,
            'rating' => 5,
        ]);

        $response->assertStatus(403);
    }
}