<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'qty', 'price', 'subtotal'];

    // Relasi item ini milik satu order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi item ini merepresentasikan satu produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
