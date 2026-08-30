<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nisn', 20)->unique();
            $table->string('kelas', 20);
            $table->string('jurusan', 50);
            $table->string('angkatan', 9);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_profiles');
    }
};
