<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use Illuminate\Support\Facades\Hash;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Auteurs de test
        $authors = [
            [
                'name' => 'Victor Hugo',
                'email' => 'victor.hugo@example.com',
                'password' => Hash::make('password123'),
                'bio' => 'Victor Hugo (1802-1885) est un poète, dramaturge, écrivain et homme politique français. Il est considéré comme l\'un des plus importants écrivains de langue française.',
                'avatar' => null,
                'website' => 'https://www.victorhugo.fr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Albert Camus',
                'email' => 'albert.camus@example.com',
                'password' => Hash::make('password123'),
                'bio' => 'Albert Camus (1913-1960) était un écrivain, philosophe, romancier et dramaturge français. Il est l\'auteur de "L\'Étranger" et "La Peste".',
                'avatar' => null,
                'website' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'George Orwell',
                'email' => 'george.orwell@example.com',
                'password' => Hash::make('password123'),
                'bio' => 'George Orwell (1903-1950) était un écrivain et journaliste britannique, célèbre pour ses romans "1984" et "La Ferme des animaux".',
                'avatar' => null,
                'website' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Austen',
                'email' => 'jane.austen@example.com',
                'password' => Hash::make('password123'),
                'bio' => 'Jane Austen (1775-1817) est une romancière anglaise dont les œuvres sont considérées comme des classiques de la littérature anglaise.',
                'avatar' => null,
                'website' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Franz Kafka',
                'email' => 'franz.kafka@example.com',
                'password' => Hash::make('password123'),
                'bio' => 'Franz Kafka (1883-1924) est un écrivain pragois de langue allemande, auteur de "La Métamorphose" et "Le Procès".',
                'avatar' => null,
                'website' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gabriel García Márquez',
                'email' => 'gabriel.marquez@example.com',
                'password' => Hash::make('password123'),
                'bio' => 'Gabriel García Márquez (1927-2014) est un écrivain colombien, prix Nobel de littérature 1982, auteur de "Cent ans de solitude".',
                'avatar' => null,
                'website' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marcel Proust',
                'email' => 'marcel.proust@example.com',
                'password' => Hash::make('password123'),
                'bio' => 'Marcel Proust (1871-1922) est un écrivain français, auteur de "À la recherche du temps perdu".',
                'avatar' => null,
                'website' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }

        // Créer 20 auteurs supplémentaires avec des données aléatoires
        Author::factory()->count(20)->create();
    }
}