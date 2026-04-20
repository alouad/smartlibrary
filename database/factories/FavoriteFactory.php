<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\Reader;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'reader_id' => Reader::factory(),
            'book_id' => Book::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Favorite $favorite) {
            // Après création, on peut ajouter des relations
        });
    }
}