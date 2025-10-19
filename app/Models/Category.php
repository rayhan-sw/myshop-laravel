<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id'];

    // Scope untuk kategori utama (root)
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    // Scope untuk subkategori berdasarkan parent
    public function scopeSubsOf($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    // Relasi ke parent (jika subkategori)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relasi ke children (subkategori)
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
