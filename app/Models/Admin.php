<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['user_id', 'role'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['user_id' => 'integer',];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
