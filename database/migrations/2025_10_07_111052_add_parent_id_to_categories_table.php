<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk menambahkan relasi hierarki (parent-child) pada tabel categories
return new class extends Migration {
    // Menjalankan migrasi (menambahkan kolom dan indeks)
    public function up(): void {
        Schema::table('categories', function (Blueprint $t) {
            // Kolom parent_id bersifat opsional, memungkinkan kategori menjadi root atau subkategori
            $t->foreignId('parent_id')
              ->nullable()
              ->after('name')
              ->constrained('categories')
              ->nullOnDelete();

            // Kombinasi unik: nama kategori boleh sama asalkan berada di parent yang berbeda
            $t->unique(['parent_id','name'], 'categories_parent_name_unique');
            $t->index('parent_id');
        });
    }

    // Membatalkan migrasi (menghapus kolom dan constraint yang ditambahkan)
    public function down(): void {
        Schema::table('categories', function (Blueprint $t) {
            $t->dropUnique('categories_parent_name_unique');
            $t->dropConstrainedForeignId('parent_id');
        });
    }
};
