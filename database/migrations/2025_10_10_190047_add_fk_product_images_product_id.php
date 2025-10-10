<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('product_images', function (Blueprint $t) {
            try { $t->dropForeign(['product_id']); } catch (\Throwable $e) {}
            $t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::table('product_images', function (Blueprint $t) {
            $t->dropForeign(['product_id']);
        });
    }
};

