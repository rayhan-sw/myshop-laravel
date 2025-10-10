<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $t) {
            if (!Schema::hasColumn('products', 'is_active')) {
                $t->boolean('is_active')->default(true)->after('category_id');
            }
        });
    }

    public function down(): void {
        Schema::table('products', function (Blueprint $t) {
            if (Schema::hasColumn('products', 'is_active')) {
                $t->dropColumn('is_active');
            }
        });
    }
};
