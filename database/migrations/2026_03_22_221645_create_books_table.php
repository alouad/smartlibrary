<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('file_path');
            $table->date('published_date')->nullable();
            $table->string('isbn', 20)->unique()->nullable();
            $table->integer('page_count')->nullable();
            $table->string('language', 50)->default('français');
            $table->integer('views_count')->default(0);
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index('title');
            $table->index('author_id');
            $table->index('language');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
};