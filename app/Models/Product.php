<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',        // ← tambahkan slug ke fillable
        'description',
        'price',
        'stock',
        'category_id',
        'is_active',   // pastikan kolom ini ada di migrasi (boleh optional)
        // fallback single-image (opsional)
        'image_path',
    ];

    protected $casts = [
        'price'     => 'integer',
        'stock'     => 'integer',
        'is_active' => 'boolean',
    ];

    /* =========================
     * Route Model Binding pakai slug
     * ========================= */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* =========================
     * Model Events (slug unik + hapus anak & file)
     * ========================= */
    protected static function booted(): void
    {
        // --- Generate / jaga slug unik ---
        static::creating(function (Product $p) {
            if (empty($p->slug)) {
                $p->slug = static::makeUniqueSlug($p->name ?? 'product');
            } else {
                // jika admin mengisi slug manual, tetap pastikan unik
                $p->slug = static::makeUniqueSlug($p->slug);
            }
        });

        static::updating(function (Product $p) {
            // Jika admin mengubah NAMA & tidak mengubah slug manual → regen slug dari name
            if ($p->isDirty('name') && !$p->isDirty('slug')) {
                $p->slug = static::makeUniqueSlug($p->name ?? 'product', $p->id);
            }
            // Jika admin mengubah slug manual → tetap pastikan unik
            if ($p->isDirty('slug')) {
                $p->slug = static::makeUniqueSlug($p->slug, $p->id);
            }
        });

        // --- Hapus relasi images + file fisik saat product dihapus ---
        static::deleting(function (Product $product) {
            // Hapus semua gambar relasi via Eloquent (akan memicu hook di ProductImage untuk hapus file)
            $product->loadMissing('images');
            foreach ($product->images as $img) {
                $img->delete();
            }

            // Jika ada single image_path di tabel products, hapus juga file fisiknya
            if (!empty($product->image_path)) {
                $path = str_replace('\\', '/', $product->image_path);
                $path = preg_replace('~^public/~i', '', $path);
                Storage::disk('public')->delete($path);
            }
        });
    }

    /**
     * Generator slug unik (dipakai saat create/update).
     * $base boleh berupa "nama produk" atau slug mentah; method ini akan slugify dan memastikan unik.
     */
    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        // slugify base (kalau base sudah slug, hasilnya tetap)
        $slug = Str::slug($base) ?: Str::slug('product');
        $original = $slug;
        $i = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    /* =========================
     * Relations
     * ========================= */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        // scope ordered() ada di ProductImage -> memastikan index [0] adalah cover
        return $this->hasMany(ProductImage::class)->ordered();
    }

    /* =========================
     * Scopes
     * ========================= */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeSearch($query, ?string $q)
    {
        $q = trim((string) $q);
        if ($q === '') return $query;

        return $query->where(function ($w) use ($q) {
            $w->where('name', 'like', "%{$q}%")
              ->orWhere('description', 'like', "%{$q}%")
              ->orWhereHas('category', function ($c) use ($q) {
                  $c->where('name', 'like', "%{$q}%");
              });
        });
    }

    public function scopeUnderRoot($q, $rootId)
    {
        if (!$rootId) return $q;
        return $q->whereHas('category', fn($c) => $c->where('parent_id', $rootId));
    }

    public function scopeInSub($q, $subId)
    {
        if (!$subId) return $q;
        return $q->where('category_id', $subId);
    }

    /* =========================
     * Accessors / Helpers
     * ========================= */
    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    public function coverImage(): ?ProductImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->first();
        }
        return $this->images()->ordered()->first();
    }

    public function primaryImageUrl(): ?string
    {
        $cover = $this->coverImage();
        if ($cover) {
            return $cover->url; // accessor di ProductImage
        }
        if (!empty($this->image_path)) {
            return asset('storage/' . ltrim($this->image_path, '/'));
        }
        return null;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primaryImageUrl();
    }
}
