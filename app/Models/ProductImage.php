<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * Ganti nilai ini jika nama kolom path file di tabel berbeda
     * (mis. 'filename' atau 'file').
     */
    public const COL_PATH = 'image_path';

    protected $fillable = [
        'product_id',
        self::COL_PATH,     // gunakan konstanta, bukan hardcode string
        'is_primary',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'is_primary' => 'boolean',
    ];

    // Sertakan accessor 'url' saat toArray()/JSON
    protected $appends = ['url'];

    protected static function booted(): void
    {
        // Hapus file fisik saat record dihapus
        static::deleting(function (ProductImage $img) {
            $path = $img->{self::COL_PATH} ?? '';
            if (!$path) return;

            // Normalisasi path sebelum delete
            $path = trim(str_replace('\\', '/', $path));
            $path = preg_replace('~^/?storage/~i', '', $path); // buang prefix "/storage/"
            $path = preg_replace('~^public/~i',   '', $path);  // buang prefix "public/"

            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable $e) {
                // biarkan jika gagal; jangan blokir penghapusan record
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * URL publik RELATIF untuk frontend.
     * Selalu kembalikan path /storage/... agar ikut host & port saat ini.
     */
    public function getUrlAttribute(): string
    {
        $path = $this->{self::COL_PATH} ?? '';
        if (!$path) return '';

        // Normalisasi path agar tidak ganda
        $path = trim(str_replace('\\', '/', $path));
        $path = preg_replace('~^/?storage/~i', '', $path); // hapus '/storage/' jika ada
        $path = preg_replace('~^public/~i',   '', $path);  // hapus 'public/' jika ada

        // Kembalikan URL relatif → otomatis ikut host/port (127.0.0.1:8000)
        return '/storage/' . ltrim($path, '/');
    }

    /**
     * Urutkan primary dulu, lalu id (opsional).
     */
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_primary')->orderBy('id');
    }
}
