<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','parent_id'];

    // Kategori utama (root) tidak punya parent
    public function scopeRoots($q){ return $q->whereNull('parent_id'); }

    // Ambil subkategori dari parent tertentu
    public function scopeSubsOf($q, $parentId){ return $q->where('parent_id', $parentId); }

    public function parent(){ return $this->belongsTo(Category::class, 'parent_id'); }
    public function children(){ return $this->hasMany(Category::class, 'parent_id'); }
}
