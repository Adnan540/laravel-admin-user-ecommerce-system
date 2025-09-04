<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    // If you're NOT using spatie/laravel-translatable, remove this.
    // If you ARE using it, the property name must be $translatable (not $tranlatable).
    // public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'name_en',
        'description',
        'description_en',
        'price',
        'discount_price',
        'category_id',
        'weight',
        'weight_unit',
        'image_url',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    // Add computed URL to API responses
    protected $appends = ['image_full_url'];

    public function getImageFullUrlAttribute(): ?string
    {
        if (!$this->image_url) {
            return null;
        }

        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($this->image_url);
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
