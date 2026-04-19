<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Roman',
                'slug' => 'roman',
                'description' => 'Romans et fiction littéraire. Œuvres narratives en prose.',
                'color' => '#3498db',
            ],
            [
                'name' => 'Science-Fiction',
                'slug' => 'science-fiction',
                'description' => 'Livres de science-fiction, anticipation et univers futuristes.',
                'color' => '#9b59b6',
            ],
            [
                'name' => 'Policier',
                'slug' => 'policier',
                'description' => 'Romans policiers, thrillers et enquêtes criminelles.',
                'color' => '#e74c3c',
            ],
            [
                'name' => 'Histoire',
                'slug' => 'histoire',
                'description' => 'Livres historiques, biographies historiques.',
                'color' => '#f39c12',
            ],
            [
                'name' => 'Biographie',
                'slug' => 'biographie',
                'description' => 'Biographies, mémoires et autobiographies.',
                'color' => '#2ecc71',
            ],
            [
                'name' => 'Philosophie',
                'slug' => 'philosophie',
                'description' => 'Ouvrages philosophiques et essais.',
                'color' => '#1abc9c',
            ],
            [
                'name' => 'Poésie',
                'slug' => 'poesie',
                'description' => 'Recueils de poèmes et œuvres poétiques.',
                'color' => '#e67e22',
            ],
            [
                'name' => 'Jeunesse',
                'slug' => 'jeunesse',
                'description' => 'Livres pour enfants et adolescents.',
                'color' => '#f1c40f',
            ],
            [
                'name' => 'Technologie',
                'slug' => 'technologie',
                'description' => 'Informatique, programmation et nouvelles technologies.',
                'color' => '#34495e',
            ],
            [
                'name' => 'Science',
                'slug' => 'science',
                'description' => 'Ouvrages scientifiques, physique, chimie, biologie.',
                'color' => '#16a085',
            ],
            [
                'name' => 'Art',
                'slug' => 'art',
                'description' => 'Art, peinture, sculpture, photographie.',
                'color' => '#d35400',
            ],
            [
                'name' => 'Voyage',
                'slug' => 'voyage',
                'description' => 'Récits de voyage, guides touristiques.',
                'color' => '#2980b9',
            ],
            [
                'name' => 'Développement Personnel',
                'slug' => 'developpement-personnel',
                'description' => 'Livres de développement personnel et bien-être.',
                'color' => '#27ae60',
            ],
            [
                'name' => 'Fantasy',
                'slug' => 'fantasy',
                'description' => 'Romans de fantasy, magie et mondes imaginaires.',
                'color' => '#8e44ad',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}