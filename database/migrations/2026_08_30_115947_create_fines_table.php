<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_item_id')->unique()->constrained('borrowing_items')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('reason', ['telat', 'rusak', 'hilang'])->default('telat');
            $table->enum('status', ['belum_dibayar', 'lunas'])->default('belum_dibayar');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
