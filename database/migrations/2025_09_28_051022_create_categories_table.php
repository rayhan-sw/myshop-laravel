<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                    // id
            $table->string('name')->unique(); // name
            $table->timestamps();            // created_at & updated_at (created_at yang kamu minta ikut otomatis)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
