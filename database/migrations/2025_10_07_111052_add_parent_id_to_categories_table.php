<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('categories', function (Blueprint $t) {
            // parent_id nullable → kategori bisa jadi root (utama) atau anak (sub)
            $t->foreignId('parent_id')
              ->nullable()
              ->after('name')
              ->constrained('categories')
              ->nullOnDelete();

            // Unik per parent: nama boleh sama di parent yg berbeda (mis. "T-Shirt" di Pria & Wanita)
            $t->unique(['parent_id','name'], 'categories_parent_name_unique');
            $t->index('parent_id');
        });
    }

    public function down(): void {
        Schema::table('categories', function (Blueprint $t) {
            $t->dropUnique('categories_parent_name_unique');
            $t->dropConstrainedForeignId('parent_id');
        });
    }
};
