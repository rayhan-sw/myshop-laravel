<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            if (!Schema::hasColumn('products', 'slug')) {
                $t->string('slug', 191)->nullable()->unique()->after('name');
            }
        });

        // Backfill slug untuk data lama
        $rows = DB::table('products')->select('id','name','slug')->get();
        $used = [];
        foreach ($rows as $r) {
            if ($r->slug) { $used[$r->slug] = true; continue; }
            $base = Str::slug($r->name ?: ('product-'.$r->id));
            $slug = $base ?: ('product-'.$r->id);

            $i = 1;
            $candidate = $slug;
            while (isset($used[$candidate]) || DB::table('products')->where('slug',$candidate)->where('id','!=',$r->id)->exists()) {
                $candidate = $slug.'-'.$i++;
            }
            DB::table('products')->where('id',$r->id)->update(['slug'=>$candidate]);
            $used[$candidate] = true;
        }
    }

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
