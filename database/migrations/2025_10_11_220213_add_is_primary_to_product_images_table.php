<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('product_images', function (Blueprint $table) {
            if (!Schema::hasColumn('product_images', 'is_primary')) {
                if (Schema::hasColumn('product_images', 'image_path')) {
                    $table->boolean('is_primary')->default(false)->after('image_path');
                } else {
                    $table->boolean('is_primary')->default(false);
                }
            }
        });
    }

    public function down(): void {
        Schema::table('product_images', function (Blueprint $table) {
            if (Schema::hasColumn('product_images', 'is_primary')) {
                $table->dropColumn('is_primary');
            }
        });
    }
};
