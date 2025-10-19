<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // Sumber gambar yang tadi kamu salin
        $src = database_path('seeders/media/products');

        // Tujuan: folder disk "public" → nanti diakses via /storage/products/...
        $dst = storage_path('app/public/products');

        // Pastikan folder tujuan ada
        File::ensureDirectoryExists($dst);

        if (!File::exists($src)) {
            $this->command?->warn("Folder sumber gambar tidak ditemukan: {$src}");
            return;
        }

        // Salin semua file & subfolder dari src → dst (rekursif)
        File::copyDirectory($src, $dst);

        $this->command?->info('Media produk disalin ke: ' . public_path('storage/products'));
    }
}
