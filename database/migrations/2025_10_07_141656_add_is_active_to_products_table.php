<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk menambahkan kolom status aktif pada tabel products
return new class extends Migration {
    // Menjalankan migrasi (menambahkan kolom is_active jika belum ada)
    public function up(): void {
        Schema::table('products', function (Blueprint $t) {
            if (!Schema::hasColumn('products', 'is_active')) {
                $t->boolean('is_active')->default(true)->after('category_id'); // Menandai apakah produk aktif atau tidak
            }
        });
    }

    // Membatalkan migrasi (menghapus kolom is_active jika ada)
    public function down(): void {
        Schema::table('products', function (Blueprint $t) {
            if (Schema::hasColumn('products', 'is_active')) {
                $t->dropColumn('is_active');
            }
        });
    }
};
