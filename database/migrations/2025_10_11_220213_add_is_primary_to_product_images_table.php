<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk menambahkan kolom is_primary pada tabel product_images
return new class extends Migration {
    public function up(): void {
        Schema::table('product_images', function (Blueprint $table) {
            if (!Schema::hasColumn('product_images', 'is_primary')) { // Cek jika kolom belum ada
                if (Schema::hasColumn('product_images', 'image_path')) {
                    $table->boolean('is_primary')->default(false)->after('image_path'); // Menandai gambar utama produk
                } else {
                    $table->boolean('is_primary')->default(false); // Tambahkan kolom jika image_path tidak ada
                }
            }
        });
    }

    public function down(): void {
        Schema::table('product_images', function (Blueprint $table) {
            if (Schema::hasColumn('product_images', 'is_primary')) { // Cek jika kolom sudah ada
                $table->dropColumn('is_primary'); // Hapus kolom is_primary saat rollback
            }
        });
    }
};
