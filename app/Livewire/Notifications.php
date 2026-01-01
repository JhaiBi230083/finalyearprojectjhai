<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Notifications extends Component
{
    use WithPagination;

    public $unreadCount = 0;

    protected $listeners = [
        'refresh-notifications' => '$refresh',
        'order-status-updated' => 'handleOrderStatusUpdate'
    ];

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function render()
    {
        $notifications = NotificationModel::query()
            ->where('user_id', Auth::id())
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.notifications', [
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($notificationId)
    {
        $notification = NotificationModel::where('user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->markAsRead();
        $this->updateUnreadCount();

        // If it's an order-related notification, redirect to order details
        if ($notification->order_id && Auth::user()->isStudent()) {
            return redirect()->route('order.details', $notification->order_id);
        }

        // Refresh the component
        $this->dispatch('refresh-notifications');
    }

    public function handleOrderStatusUpdate($orderId, $status)
    {
        // This will automatically refresh the notifications when status changes
        $this->updateUnreadCount();
        $this->dispatch('refresh-notifications');
    }

    private function updateUnreadCount()
    {
        $this->unreadCount = NotificationModel::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    // Helper method to get notification icon
    public function getNotificationIcon($type)
    {
        return match($type) {
            'order_placed' => '🛒',
            'preparation_started' => '👨‍🍳',
            'order_ready' => '✅',
            'order_completed' => '🎉',
            'order_cancelled' => '❌',
            default => '📢'
        };
    }

    // Helper method to get notification color
    public function getNotificationColor($type)
    {
        return match($type) {
            'order_placed' => 'bg-blue-50 border-blue-200',
            'preparation_started' => 'bg-orange-50 border-orange-200',
            'order_ready' => 'bg-green-50 border-green-200',
            'order_completed' => 'bg-purple-50 border-purple-200',
            'order_cancelled' => 'bg-red-50 border-red-200',
            default => 'bg-gray-50 border-gray-200'
        };
    }

    // Format time difference for display
    public function timeAgo($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }
}
