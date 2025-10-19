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
        'slug',
        'description',
        'price',
        'stock',
        'category_id',
        'is_active',
        'image_path',
    ];

    protected $casts = [
        'price'     => 'integer',
        'stock'     => 'integer',
        'is_active' => 'boolean',
    ];

    // Bind route ke kolom 'slug'
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        // Slug otomatis
        static::creating(function (Product $p) {
            $p->slug = static::makeUniqueSlug($p->slug ?: $p->name ?: 'product');
        });

        static::updating(function (Product $p) {
            if ($p->isDirty('name') && !$p->isDirty('slug')) {
                $p->slug = static::makeUniqueSlug($p->name ?? 'product', $p->id);
            }
            if ($p->isDirty('slug')) {
                $p->slug = static::makeUniqueSlug($p->slug, $p->id);
            }
        });

        // Hapus semua image saat product dihapus
        static::deleting(function (Product $product) {
            $product->loadMissing('images');
            foreach ($product->images as $img) {
                $img->delete();
            }

            if (!empty($product->image_path)) {
                $path = str_replace('\\', '/', $product->image_path);
                $path = preg_replace('~^public/~i', '', $path);
                Storage::disk('public')->delete($path);
            }
        });
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'product';
        $original = $slug;
        $i = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        // Urutkan agar gambar utama muncul lebih dulu
        return $this->hasMany(ProductImage::class)
            ->orderByDesc('is_primary')
            ->orderBy('id');
    }

    // Scopes
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

    // Helpers / Accessors
    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    public function coverImage(): ?ProductImage
    {
        // karena sudah diurutkan, cukup ambil pertama
        return $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->first();
    }

    public function primaryImageUrl(): ?string
    {
        $cover = $this->coverImage();
        if ($cover) return $cover->url;

        return $this->image_path
            ? asset('storage/' . ltrim($this->image_path, '/'))
            : null;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primaryImageUrl();
    }
}
