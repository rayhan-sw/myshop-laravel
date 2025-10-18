<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel carts
return new class extends Migration {
    // Menjalankan migrasi (membuat tabel carts)
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();// Primary key keranjang
            $table->foreignId('user_id')->unique()// Relasi ke tabel users (satu user satu cart)
                  ->constrained()
                  ->cascadeOnDelete();
            $table->timestamps();// Kolom created_at & updated_at
        });
    }

    // Membatalkan migrasi (menghapus tabel carts)
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
