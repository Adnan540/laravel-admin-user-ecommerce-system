<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model

{
    protected $fillable = ['user_id', 'product_id', 'cart_id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['user_id' => 'integer',];
    protected $appends = ['total_price'];
    use HasFactory;
    public function getTotalPriceAttribute()
    {
        return $this->cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
