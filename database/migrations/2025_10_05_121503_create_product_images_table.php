<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel product_images
return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id(); // Primary key gambar produk
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel products
            $table->string('image_path'); // Lokasi penyimpanan gambar di disk 'public'
            $table->unsignedInteger('sort_order')->default(0); // Urutan tampilan gambar
            $table->timestamps(); // Kolom created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images'); // Hapus tabel product_images saat rollback
    }
};
