<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Scope for active images
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered images
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    // Get full image URL
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset('images/default-dashboard.jpg');
        }

        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }

    // Get storage path
    public function getStoragePathAttribute()
    {
        if (str_starts_with($this->image_path, 'storage/')) {
            return str_replace('storage/', '', $this->image_path);
        }
        return $this->image_path;
    }
}
