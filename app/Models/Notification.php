<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'title',
        'message',
        'type',
        'is_read',
        'metadata',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
        'read_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    public static function createOrderNotification($order, $action, $performedBy)
    {
        $user = $order->user;
        $vendor = $order->meal->canteen->vendor;
        $mealName = $order->meal->name;

        $notificationData = [
            'order_id' => $order->id,
            'metadata' => [
                'action' => $action,
                'performed_by' => $performedBy->id,
                'order_number' => $order->order_number,
                'meal_name' => $mealName,
            ]
        ];

        switch ($action) {
            case 'order_placed':
                // Notify vendor when student places order
                self::create([
                    'user_id' => $vendor->id,
                    'title' => 'New Order Received',
                    'message' => "New order #{$order->order_number} for {$mealName} from {$user->name}",
                    'type' => 'order_placed',
                    ...$notificationData
                ]);
                break;

            case 'start_prep':
                // Notify student when vendor starts preparation
                self::create([
                    'user_id' => $user->id,
                    'title' => 'Order Preparation Started',
                    'message' => "Your order #{$order->order_number} for {$mealName} is now being prepared",
                    'type' => 'preparation_started',
                    ...$notificationData
                ]);
                break;

            case 'mark_ready':
                // Notify student when order is ready
                self::create([
                    'user_id' => $user->id,
                    'title' => 'Order Ready for Pickup',
                    'message' => "Your order #{$order->order_number} for {$mealName} is ready for pickup!",
                    'type' => 'order_ready',
                    ...$notificationData
                ]);
                break;

            case 'complete':
                // Notify student when order is completed
                self::create([
                    'user_id' => $user->id,
                    'title' => 'Order Completed',
                    'message' => "Order #{$order->order_number} for {$mealName} has been completed. Thank you!",
                    'type' => 'order_completed',
                    ...$notificationData
                ]);
                break;

            case 'cancel':
                // Notify both parties when order is cancelled
                if ($performedBy->isVendor()) {
                    // Vendor cancelled - notify student
                    self::create([
                        'user_id' => $user->id,
                        'title' => 'Order Cancelled by Vendor',
                        'message' => "Order #{$order->order_number} for {$mealName} was cancelled by the vendor",
                        'type' => 'order_cancelled',
                        ...$notificationData
                    ]);
                } else {
                    // Student cancelled - notify vendor
                    self::create([
                        'user_id' => $vendor->id,
                        'title' => 'Order Cancelled by Student',
                        'message' => "Order #{$order->order_number} for {$mealName} was cancelled by {$user->name}",
                        'type' => 'order_cancelled',
                        ...$notificationData
                    ]);
                }
                break;
        }
    }
}
