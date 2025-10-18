<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel categories
return new class extends Migration {
    // Menjalankan migrasi (membuat tabel categories)
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                    // Primary key kategori
            $table->string('name')->unique(); // Nama kategori (tidak boleh duplikat)
            $table->timestamps();            // Kolom created_at dan updated_at otomatis
        });
    }

    // Membatalkan migrasi (menghapus tabel categories)
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
