<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'discount_price', 'category_id', 'weight', 'weight_unit', 'image_url'];
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    //product belongs to a one category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //product has many wishlists
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
    //product has many cart items
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    //product has many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
