<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(rand(2, 6));
        $title = rtrim($title, '.');
        
        return [
            'title' => $title,
            'author_id' => Author::factory(),
            'description' => $this->faker->paragraphs(rand(3, 10), true),
            'cover_image' => null,
            'file_path' => 'books/pdfs/' . Str::slug($title) . '.pdf',
            'published_date' => $this->faker->dateTimeBetween('-50 years', 'now'),
            'isbn' => $this->faker->optional(0.8)->isbn13(),
            'page_count' => $this->faker->numberBetween(100, 1200),
            'language' => $this->faker->randomElement(['français', 'anglais', 'espagnol', 'allemand', 'italien']),
            'views_count' => $this->faker->numberBetween(0, 10000),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the book has a cover image.
     */
    public function withCover(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_image' => 'books/covers/' . Str::slug($attributes['title']) . '.jpg',
        ]);
    }

    /**
     * Indicate that the book is in French.
     */
    public function french(): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => 'français',
        ]);
    }

    /**
     * Indicate that the book is popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views_count' => $this->faker->numberBetween(5000, 50000),
        ]);
    }

    /**
     * Indicate that the book has a specific number of pages.
     */
    public function withPages(int $pages): static
    {
        return $this->state(fn (array $attributes) => [
            'page_count' => $pages,
        ]);
    }

    /**
     * Indicate that the book is published recently.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Book $book) {
            // Après création, on peut ajouter des relations
            if ($this->faker->boolean(30)) {
                $book->increment('views_count', $this->faker->numberBetween(1, 1000));
            }
        });
    }
}