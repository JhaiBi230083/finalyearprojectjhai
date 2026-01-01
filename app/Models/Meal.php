<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'nutritional_info',
        'is_available',
        'image_url',
        'preparation_time',
        'canteen_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'nutritional_info' => 'array',
    ];

    // Relationships
    public function canteen()
    {
        return $this->belongsTo(Canteen::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }
// In App\Models\Meal.php
    public function getFullImageUrlAttribute()
    {
        if (!$this->image_url) {
            return asset('images/default-meal.jpg');
        }

        // If it's already a full URL (stored from external source)
        if (str_starts_with($this->image_url, 'http://') ||
            str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }

        // If it's a local storage path
        return asset('storage/' . $this->image_url);
    }
    // Methods
    public function averageRating()
    {
        return $this->feedback()->avg('rating') ?? 0;
    }

    public function totalOrders()
    {
        return $this->orders()->count();
    }
}
