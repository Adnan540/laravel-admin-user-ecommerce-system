<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // define relation btwn contact message and user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // define relation btwn contact message and admin
    // this is used to get the admin who responded to the contact message
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
