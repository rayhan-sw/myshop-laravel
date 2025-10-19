<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    // Nama kolom path file
    public const COL_PATH = 'image_path';

    protected $fillable = [
        'product_id',
        self::COL_PATH,
        'is_primary',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'is_primary' => 'boolean',
    ];

    // Tambahkan atribut 'url' ke output JSON
    protected $appends = ['url'];

    protected static function booted(): void
    {
        // Hapus file fisik saat record dihapus
        static::deleting(function (ProductImage $img) {
            $path = $img->{self::COL_PATH} ?? '';
            if (!$path) return;

            // Normalisasi path agar aman
            $path = trim(str_replace('\\', '/', $path));
            $path = preg_replace('~^/?storage/~i', '', $path);
            $path = preg_replace('~^public/~i', '', $path);

            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable $e) {
                // Abaikan error agar tidak ganggu penghapusan record
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Dapatkan URL publik relatif untuk frontend
    public function getUrlAttribute(): string
    {
        $path = $this->{self::COL_PATH} ?? '';
        if (!$path) return '';

        $path = trim(str_replace('\\', '/', $path));
        $path = preg_replace('~^/?storage/~i', '', $path);
        $path = preg_replace('~^public/~i', '', $path);

        return '/storage/' . ltrim($path, '/');
    }

    // Scope urutkan agar gambar utama (primary) muncul dulu
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_primary')->orderBy('id');
    }
}
