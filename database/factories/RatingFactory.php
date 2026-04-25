<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\Reader;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $reviews = [
            "Excellent livre, je l'ai dévoré en une soirée !",
            "Très bon ouvrage, je recommande vivement.",
            "Intéressant mais un peu long par moments.",
            "Un classique incontournable.",
            "L'auteur nous transporte dans son univers.",
            "Déçu, je m'attendais à mieux.",
            "Superbe écriture, personnages attachants.",
            "À lire absolument !",
            "Un peu difficile au début mais captivant ensuite.",
            "Magnifique histoire, j'ai adoré.",
            "Très bien documenté, j'ai appris beaucoup.",
            "Style d'écriture fluide et agréable.",
            "Un livre qui fait réfléchir.",
            "Une lecture rafraîchissante.",
            "J'ai été transporté du début à la fin.",
        ];
        
        $rating = $this->faker->numberBetween(1, 5);
        
        return [
            'reader_id' => Reader::factory(),
            'book_id' => Book::factory(),
            'rating' => $rating,
            'review' => $this->faker->optional(0.6)->randomElement($reviews),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the rating is high (4 or 5 stars).
     */
    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(4, 5),
        ]);
    }

    /**
     * Indicate that the rating is low (1 or 2 stars).
     */
    public function low(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
        ]);
    }

    /**
     * Indicate that the rating has a review.
     */
    public function withReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'review' => $this->faker->paragraph(),
        ]);
    }

    /**
     * Indicate a specific rating value.
     */
    public function value(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $rating,
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Rating $rating) {
            // Après création, on peut ajouter des relations
        });
    }
}