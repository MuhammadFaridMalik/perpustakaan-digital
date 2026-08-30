<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('users');
            $table->foreignId('book_id')->constrained('books');
            $table->enum('status', ['menunggu', 'siap_diambil', 'selesai', 'dibatalkan', 'kadaluarsa'])->default('menunggu');
            $table->timestamp('booked_at');
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
