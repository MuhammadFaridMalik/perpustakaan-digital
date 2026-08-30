<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('isbn', 20)->nullable()->unique();
            $table->text('synopsis')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('author_id')->constrained('authors');
            $table->foreignId('publisher_id')->nullable()->constrained('publishers')->nullOnDelete();
            $table->foreignId('rack_id')->nullable()->constrained('racks')->nullOnDelete();
            $table->year('published_year')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
