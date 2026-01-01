<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canteen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'opening_time',
        'closing_time',
        'is_active',
        'vendor_id',
    ];

    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function activeOrders()
    {
        return $this->hasManyThrough(Order::class, Meal::class)
            ->whereIn('status', ['pending', 'confirmed', 'preparing']);
    }

    // Methods
    public function isOpen()
    {
        $now = now();
        return $this->is_active &&
            $now->between(
                $this->opening_time,
                $this->closing_time
            );
    }

    public function currentQueueCount()
    {
        return $this->activeOrders()->count();
    }

    public function estimatedWaitTime()
    {
        $activeOrders = $this->activeOrders()->count();
        $avgPrepTime = $this->meals()->avg('preparation_time') ?? 10;

        return $activeOrders * $avgPrepTime;
    }
}
