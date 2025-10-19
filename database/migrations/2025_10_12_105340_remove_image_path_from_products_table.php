<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk mengubah kolom image_path pada tabel products menjadi nullable
return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable()->change(); // Izinkan kolom image_path bernilai null
        });
    }

    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change(); // Kembalikan kolom image_path menjadi wajib diisi
        });
    }
};
