<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel order_items
return new class extends Migration {
    public function up(): void {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // Primary key item pesanan
            $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel orders
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel products
            $table->integer('qty'); // Jumlah produk dalam pesanan
            $table->decimal('price', 12, 2); // Harga per unit produk
            $table->decimal('subtotal', 12, 2); // Total harga (qty × price)
            $table->timestamps(); // Kolom created_at & updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('order_items'); // Hapus tabel order_items saat rollback
    }
};
