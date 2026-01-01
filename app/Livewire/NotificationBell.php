<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $recentNotifications = [];
    public $showDropdown = false;

    protected $listeners = [
        'refresh-notifications' => '$refresh',
        'order-status-updated' => 'updateCount'
    ];

    public function mount()
    {
        $this->updateCount();
    }

    public function updatedShowDropdown()
    {
        if ($this->showDropdown) {
            $this->updateCount();
        }
    }

    public function updateCount()
    {
        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        $this->recentNotifications = Notification::where('user_id', Auth::id())
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
        if ($this->showDropdown) {
            $this->updateCount();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->markAsRead();
        $this->updateCount();

        // Redirect based on user role - FIXED route name
        if ($notification->order_id) {
            if (Auth::user()->isStudent()) {
                return redirect()->route('order.details', $notification->order_id); // Changed to 'order.details'
            } elseif (Auth::user()->isVendor()) {
                return redirect()->route('vendor.orders');
            }
        }

        // Refresh other components
        $this->dispatch('refresh-notifications');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $this->updateCount();
        $this->dispatch('refresh-notifications');
    }

    public function goToNotificationsPage()
    {
        return redirect()->route('notifications');
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
