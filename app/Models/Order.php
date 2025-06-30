<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'shipping_address_id',
        'status',
        'total_amount',
        'payment_status',
        'total_price',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
