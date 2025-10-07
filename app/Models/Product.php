<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        // Legacy single-image path (dipertahankan sebagai fallback)
        'image_path',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        // ordered() ada di ProductImage
        return $this->hasMany(ProductImage::class)->ordered();
    }

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

    /**
     * Scope: pencarian sederhana pada name, description, dan nama kategori.
     * Pemakaian: Product::search($q)->...
     */
    public function scopeSearch($query, ?string $q)
    {
        $q = trim((string) $q);
        if ($q === '') {
            return $query;
        }

        return $query->where(function ($w) use ($q) {
            $w->where('name', 'like', "%{$q}%")
              ->orWhere('description', 'like', "%{$q}%")
              ->orWhereHas('category', function ($c) use ($q) {
                  $c->where('name', 'like', "%{$q}%");
              });
        });
    }
}
