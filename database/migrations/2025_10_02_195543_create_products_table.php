<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel products
return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key produk
            $table->string('name', 150)->nullable(false); // Nama produk
            $table->text('description')->nullable(false); // Deskripsi produk
            $table->decimal('price', 12, 2)->nullable(false); // Harga produk
            $table->unsignedInteger('stock')->default(0)->nullable(false); // Jumlah stok produk
            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel categories
            $table->string('image_path')->nullable(); // Path gambar produk (opsional)
            $table->timestamps(); // Kolom created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
