<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        
        return [
            'name' => $firstName . ' ' . $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'bio' => $this->faker->paragraphs(rand(2, 5), true),
            'avatar' => $this->faker->optional(0.4)->imageUrl(200, 200, 'people', true, $firstName),
            'website' => $this->faker->optional(0.3)->url(),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the author has a specific genre.
     */
    public function withGenre(string $genre): static
    {
        return $this->state(fn (array $attributes) => [
            'bio' => "Auteur spécialisé dans le genre $genre. " . $this->faker->paragraph(),
        ]);
    }

    /**
     * Indicate that the author is famous.
     */
    public function famous(): static
    {
        return $this->state(fn (array $attributes) => [
            'bio' => $this->faker->paragraphs(5, true) . "\n\nPrix littéraires : " . $this->faker->sentence(),
            'website' => 'https://' . $this->faker->domainName(),
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Author $author) {
            // Après création, on peut ajouter des relations
            if ($this->faker->boolean(20)) {
                $author->website = 'https://' . Str::slug($author->name) . '.com';
                $author->save();
            }
        });
    }
}