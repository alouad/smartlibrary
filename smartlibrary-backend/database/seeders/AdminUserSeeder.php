<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reader;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un administrateur
        Reader::create([
            'name' => 'Administrateur',
            'email' => 'admin@smartlibrary.com',
            'password' => Hash::make('admin123456'),
            'avatar' => null,
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer un super administrateur (optionnel)
        Reader::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@smartlibrary.com',
            'password' => Hash::make('superadmin123456'),
            'avatar' => null,
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}