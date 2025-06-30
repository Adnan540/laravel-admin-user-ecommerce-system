<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraderApplication extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'business_name',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // Define relation between TraderApplication and User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Define relation between TraderApplication and Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
