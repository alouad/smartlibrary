<?php

namespace Database\Factories;

use App\Models\ReadingProgress;
use App\Models\Reader;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadingProgressFactory extends Factory
{
    protected $model = ReadingProgress::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $reader = Reader::factory();
        $book = Book::factory();
        
        // Créer un livre avec un nombre de pages
        $bookInstance = $book->create();
        
        $currentPage = $this->faker->numberBetween(0, $bookInstance->page_count);
        $progressPercent = $bookInstance->page_count > 0 
            ? ($currentPage / $bookInstance->page_count) * 100 
            : 0;
        
        return [
            'reader_id' => $reader,
            'book_id' => $bookInstance->id,
            'current_page' => $currentPage,
            'progress_percent' => $progressPercent,
            'last_read_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'is_completed' => $progressPercent >= 100,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the book is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_page' => function (array $attributes) {
                $book = Book::find($attributes['book_id']);
                return $book ? $book->page_count : 0;
            },
            'progress_percent' => 100,
            'is_completed' => true,
        ]);
    }

    /**
     * Indicate that the book is not started yet.
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_page' => 0,
            'progress_percent' => 0,
            'is_completed' => false,
        ]);
    }

    /**
     * Indicate a specific progress percentage.
     */
    public function progress(int $percentage): static
    {
        return $this->state(function (array $attributes) use ($percentage) {
            $book = Book::find($attributes['book_id']);
            $currentPage = $book ? (int) (($percentage / 100) * $book->page_count) : 0;
            
            return [
                'current_page' => $currentPage,
                'progress_percent' => $percentage,
                'is_completed' => $percentage >= 100,
            ];
        });
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (ReadingProgress $progress) {
            // Après création, on peut ajouter des relations
        });
    }
}