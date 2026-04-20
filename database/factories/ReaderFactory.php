<?php

namespace Database\Factories;

use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReaderFactory extends Factory
{
    protected $model = Reader::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'avatar' => $this->faker->optional(0.3)->imageUrl(200, 200, 'people'),
            'is_admin' => false,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    /**
     * Indicate that the user has a specific email domain.
     */
    public function withDomain(string $domain): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $this->faker->unique()->userName() . '@' . $domain,
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Reader $reader) {
            // Après création, on peut ajouter des relations
            if ($this->faker->boolean(30)) {
                $reader->avatar = $this->faker->imageUrl(200, 200, 'people', true, $reader->name);
                $reader->save();
            }
        });
    }
}