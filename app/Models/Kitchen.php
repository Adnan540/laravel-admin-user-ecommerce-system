<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Kitchen extends Model
{
    use HasFactory;

    protected $table = 'kitchen';

    protected $fillable = [
        'title',
        'title_en',
        'description',
        'description_en',
        'image',
        'price',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    // Add computed field to API JSON
    protected $appends = ['image_full_url'];

    public function getImageFullUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If already a full URL (http/https), just return it
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Otherwise build from local storage
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($this->image);
    }

    // Define relationships here if needed later
}
