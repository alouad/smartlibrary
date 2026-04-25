<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reader_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->integer('current_page')->default(0);
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->timestamp('last_read_at')->useCurrent();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            
            $table->unique(['reader_id', 'book_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reading_progress');
    }
};