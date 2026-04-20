<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reader;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test l'inscription d'un lecteur
     */
    public function test_reader_can_register(): void
    {
        $response = $this->postJson('/api/auth/register/reader', [
            'name' => 'Test Reader',
            'email' => 'reader@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'access_token',
                     'token_type',
                     'user' => ['id', 'name', 'email']
                 ]);

        $this->assertDatabaseHas('readers', [
            'email' => 'reader@test.com',
            'name' => 'Test Reader'
        ]);
    }

    /**
     * Test l'inscription d'un auteur
     */
    public function test_author_can_register(): void
    {
        $response = $this->postJson('/api/auth/register/author', [
            'name' => 'Test Author',
            'email' => 'author@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'bio' => 'This is a test biography',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'access_token',
                     'token_type',
                     'user' => ['id', 'name', 'email', 'bio']
                 ]);

        $this->assertDatabaseHas('authors', [
            'email' => 'author@test.com',
            'name' => 'Test Author',
            'bio' => 'This is a test biography'
        ]);
    }

    /**
     * Test l'inscription avec email déjà utilisé
     */
    public function test_cannot_register_with_existing_email(): void
    {
        // Créer un lecteur existant
        Reader::create([
            'name' => 'Existing User',
            'email' => 'existing@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/register/reader', [
            'name' => 'New User',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test la connexion d'un lecteur
     */
    public function test_reader_can_login(): void
    {
        // Créer un lecteur
        $reader = Reader::create([
            'name' => 'Login Test',
            'email' => 'login@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@test.com',
            'password' => 'password123',
            'type' => 'reader'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'access_token',
                     'token_type',
                     'user',
                     'type'
                 ]);
    }

    /**
     * Test la connexion d'un auteur
     */
    public function test_author_can_login(): void
    {
        // Créer un auteur
        $author = Author::create([
            'name' => 'Author Login',
            'email' => 'author@test.com',
            'password' => Hash::make('password123'),
            'bio' => 'Test bio',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'author@test.com',
            'password' => 'password123',
            'type' => 'author'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'type' => 'author'
                 ]);
    }

    /**
     * Test la connexion avec mot de passe incorrect
     */
    public function test_cannot_login_with_wrong_password(): void
    {
        // Créer un lecteur
        Reader::create([
            'name' => 'Wrong Password',
            'email' => 'wrong@test.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong@test.com',
            'password' => 'wrongpassword',
            'type' => 'reader'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test la déconnexion
     */
    public function test_user_can_logout(): void
    {
        // Créer un lecteur
        $reader = Reader::create([
            'name' => 'Logout Test',
            'email' => 'logout@test.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $reader->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Déconnexion réussie'
                 ]);

        // Vérifier que le token a été supprimé
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $reader->id,
        ]);
    }

    /**
     * Test la récupération du profil
     */
    public function test_authenticated_user_can_access_profile(): void
    {
        $reader = Reader::create([
            'name' => 'Profile Test',
            'email' => 'profile@test.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $reader->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/profile');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $reader->id,
                         'name' => 'Profile Test',
                         'email' => 'profile@test.com'
                     ]
                 ]);
    }

    /**
     * Test que l'utilisateur non authentifié ne peut pas accéder au profil
     */
    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(401);
    }
}