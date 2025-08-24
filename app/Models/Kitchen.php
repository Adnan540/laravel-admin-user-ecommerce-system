<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model
{
    use HasFactory;

    protected $table = 'kitchen';

    protected $fillable = [
        'title',
        'description',
        'image',
        'price',
    ];
    // Define any relationships if needed
}
