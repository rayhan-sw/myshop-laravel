<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk memperbarui relasi foreign key antara tabel product_images dan products
return new class extends Migration {
    // Menjalankan migrasi (mengatur ulang relasi dengan aturan penghapusan cascade)
    public function up(): void {
        Schema::table('product_images', function (Blueprint $t) {
            try { 
                $t->dropForeign(['product_id']); // Hapus relasi lama jika sudah ada
            } catch (\Throwable $e) {} // Abaikan error jika constraint belum ada

            // Tambahkan kembali foreign key dengan onDelete('cascade')
            $t->foreign('product_id')
              ->references('id')
              ->on('products')
              ->onDelete('cascade');
        });
    }

    // Membatalkan migrasi (menghapus foreign key)
    public function down(): void {
        Schema::table('product_images', function (Blueprint $t) {
            $t->dropForeign(['product_id']);
        });
    }
};