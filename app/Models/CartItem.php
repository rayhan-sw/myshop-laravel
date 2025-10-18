<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'qty'];

    // Setiap item milik satu cart
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    // Setiap item mereferensikan satu produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
