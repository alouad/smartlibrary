<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lecteurs de test
        $readers = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean@example.com',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie@example.com',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pierre Durand',
                'email' => 'pierre@example.com',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sophie Bernard',
                'email' => 'sophie@example.com',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lucas Petit',
                'email' => 'lucas@example.com',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($readers as $reader) {
            Reader::create($reader);
        }

        // Créer 50 lecteurs supplémentaires avec des données aléatoires
        Reader::factory()->count(50)->create();
    }
}