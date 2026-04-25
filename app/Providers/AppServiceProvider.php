<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // Ajouter cette ligne

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Ajouter cette ligne pour corriger l'erreur de longueur de clé
        Schema::defaultStringLength(191);
    }
}