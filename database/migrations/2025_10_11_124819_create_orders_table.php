<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel orders
return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key pesanan
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel users
            $table->decimal('total', 12, 2); // Total nilai pesanan
            $table->string('status')->default('pending'); // Status pesanan: pending, diproses, dikirim, selesai, batal
            $table->text('address_text'); // Alamat pengiriman
            $table->timestamps(); // Kolom created_at & updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders'); // Hapus tabel orders saat rollback
    }
};
