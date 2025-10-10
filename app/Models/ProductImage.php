<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_path', // contoh: "products/abc.jpg" (disimpan di disk 'public')
        'sort_order',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'sort_order' => 'integer',
    ];

    // pastikan 'url' ikut terkirim ke Inertia/JSON
    protected $appends = ['url'];

    // === HAPUS FILE FISIK SAAT RECORD DIHAPUS ===
    protected static function booted(): void
    {
        static::deleting(function (ProductImage $img) {
            if (!$img->image_path) return;

            // Normalisasi path
            $path = trim(str_replace('\\', '/', $img->image_path));
            $path = preg_replace('~^public/~i', '', $path); // hilangkan prefix "public/"

            // Hapus dari disk 'public' (abaikan jika tidak ada)
            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable $e) {
                // diamkan saja; kita tidak ingin blokir penghapusan record
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * URL publik untuk frontend.
     * Menangani:
     * - path absolute (http/https)
     * - path yg sudah /storage/...
     * - path relatif di disk 'public' (products/xxx.jpg)
     * - path dengan backslash Windows -> jadi slash
     */
    public function getUrlAttribute(): ?string
    {
        $path = $this->image_path;
        if (!$path) return null;

        // Normalisasi: buang spasi & jadikan slash
        $path = trim(str_replace('\\', '/', $path));

        // Sudah absolute URL?
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        // Sudah mengarah ke /storage ?
        if (preg_match('~^/?storage/~i', $path)) {
            return asset(ltrim($path, '/'));
        }

        // Hilangkan prefix "public/" jika ada, supaya tidak dobel
        $path = preg_replace('~^public/~i', '', $path);

        // Jika file memang ada di disk 'public', gunakan URL resmi
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path); // -> /storage/{path}
        }

        // Fallback terakhir: konstruksi /storage/{path} apa adanya
        return asset('storage/' . ltrim($path, '/'));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
