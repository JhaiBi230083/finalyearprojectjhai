<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'meal_id',
        'quantity',
        'total_amount',
        'status',
        'pickup_time',
        'queue_number',
        'estimated_wait_time',
        'special_instructions',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'pickup_time' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePreparing($query)
    {
        return $query->where('status', 'preparing');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Methods
    public function generateOrderNumber()
    {
        return 'ORD' . now()->format('YmdHis') . $this->id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD' . now()->format('YmdHis') . rand(1000, 9999);
        });

        static::created(function ($order) {
            // Create notification for vendor when order is placed
            if ($order->meal && $order->meal->canteen && $order->meal->canteen->vendor) {
                \App\Models\Notification::createOrderNotification($order, 'order_placed', $order->user);
            }
        });

//        static::updated(function ($order) {
//            // Dispatch Livewire event for real-time updates
//            if ($order->wasChanged('status')) {
//                \Livewire\Livewire::dispatch('order-status-updated', [
//                    'orderId' => $order->id,
//                    'status' => $order->status
//                ]);
//            }
//        });
    }

    public function calculateEstimatedWaitTime()
    {
        $basePrepTime = $this->meal->preparation_time;
        $queueBefore = self::where('meal_id', $this->meal_id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->where('id', '<', $this->id)
            ->count();

        return ($queueBefore + 1) * $basePrepTime;
    }

    public function assignQueueNumber()
    {
        $lastQueue = self::where('meal_id', $this->meal_id)
            ->whereDate('created_at', today())
            ->max('queue_number') ?? 0;

        $this->queue_number = $lastQueue + 1;
        $this->save();
    }
}
