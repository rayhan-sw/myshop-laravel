<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total', 'status', 'address_text'];

    // Relasi satu order punya banyak item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi satu order dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
