<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel cart_items
return new class extends Migration {
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id(); // Primary key item keranjang
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete(); // Relasi ke tabel carts
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel products
            $table->unsignedInteger('qty'); // Jumlah produk dalam keranjang
            $table->timestamps(); // Kolom created_at & updated_at
            $table->unique(['cart_id','product_id']); // Setiap produk hanya boleh muncul satu kali per cart
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
