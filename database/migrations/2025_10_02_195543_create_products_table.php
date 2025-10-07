<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable(false);
            $table->text('description')->nullable(false);
            $table->decimal('price', 12, 2)->nullable(false);
            $table->unsignedInteger('stock')->default(0)->nullable(false);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('image_path')->nullable(); // ubah jadi nullable
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
