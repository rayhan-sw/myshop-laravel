<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel users, password_reset_tokens, dan sessions
return new class extends Migration
{
    // Menjalankan migrasi (membuat tabel)
    public function up(): void
    {
        // Tabel utama untuk data pengguna aplikasi
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('role')->default('customer'); // peran pengguna
            $table->string('phone', 16)->nullable()->unique(); // nomor telepon opsional
            $table->string('address', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps(); // created_at & updated_at
        });

        // Tabel untuk menyimpan token reset password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel untuk menyimpan sesi login pengguna
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    // Membatalkan migrasi (menghapus tabel)
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
