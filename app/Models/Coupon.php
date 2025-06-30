<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_percent',
        'expire_at',
        'status',
    ];
    use HasFactory;
    public function orders()
    {
        return $this->hasMany(Order::class); //one to many relationship
    }
}
