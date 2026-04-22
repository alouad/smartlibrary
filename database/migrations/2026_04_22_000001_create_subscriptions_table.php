<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['mensuel', 'annuel']);
            $table->timestamp('date_debut')->useCurrent();
            $table->timestamp('date_fin');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('actif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
