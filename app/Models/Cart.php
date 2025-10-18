<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    // Relasi satu cart memiliki banyak item
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
