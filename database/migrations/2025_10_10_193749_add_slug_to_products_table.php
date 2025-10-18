<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

// Migration untuk menambahkan kolom slug pada tabel products
return new class extends Migration {
    // Menjalankan migrasi (menambahkan kolom slug dan mengisi data lama)
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            // Tambahkan kolom slug jika belum ada
            if (!Schema::hasColumn('products', 'slug')) {
                $t->string('slug', 191)->nullable()->unique()->after('name');
            }
        });

        // Mengisi slug untuk data produk yang sudah ada
        $rows = DB::table('products')->select('id','name','slug')->get();
        $used = [];

        foreach ($rows as $r) {
            // Lewati jika slug sudah ada
            if ($r->slug) { 
                $used[$r->slug] = true; 
                continue; 
            }

            // Membuat slug dasar dari nama produk atau ID
            $base = Str::slug($r->name ?: ('product-'.$r->id));
            $slug = $base ?: ('product-'.$r->id);

            // Pastikan slug unik
            $i = 1;
            $candidate = $slug;
            while (isset($used[$candidate]) || DB::table('products')
                    ->where('slug', $candidate)
                    ->where('id', '!=', $r->id)
                    ->exists()) {
                $candidate = $slug.'-'.$i++;
            }

            // Simpan slug unik ke database
            DB::table('products')->where('id', $r->id)->update(['slug' => $candidate]);
            $used[$candidate] = true;
        }
    }

    // Membatalkan migrasi (menghapus kolom slug)
    public function down(): void
    {
        Schema::table('products', function (Blueprint $t) {
            if (Schema::hasColumn('products', 'slug')) {
                $t->dropUnique(['slug']);
                $t->dropColumn('slug');
            }
        });
    }
};
