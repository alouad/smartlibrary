<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        
        $colors = [
            '#3498db', '#9b59b6', '#e74c3c', '#f39c12', '#2ecc71',
            '#1abc9c', '#e67e22', '#f1c40f', '#34495e', '#16a085',
            '#d35400', '#2980b9', '#27ae60', '#8e44ad', '#c0392b'
        ];
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->optional(0.7)->paragraph(),
            'color' => $this->faker->randomElement($colors),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the category has a specific color.
     */
    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color' => $color,
        ]);
    }

    /**
     * Indicate that the category has a long description.
     */
    public function withLongDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $this->faker->paragraphs(3, true),
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Category $category) {
            // Après création, on peut ajouter des relations
            // Le slug est déjà généré automatiquement dans le modèle
        });
    }
}