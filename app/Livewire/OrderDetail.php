<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderDetail extends Component
{
    public $orderId;
    public $order;
    public $loading = true;
    public $notFound = false;

    protected $listeners = ['refreshOrder' => '$refresh'];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        try {
            $this->order = Order::with(['meal.canteen', 'payment', 'feedback'])
                ->where('id', $this->orderId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $this->loading = false;
        } catch (\Exception $e) {
            $this->notFound = true;
            $this->loading = false;
        }
    }

    public function cancelOrder()
    {
        if (!$this->order || !in_array($this->order->status, ['pending', 'confirmed'])) {
            session()->flash('error', 'Order cannot be cancelled at this stage.');
            return;
        }

        $this->order->update([
            'status' => 'cancelled'
        ]);

        if ($this->order->payment) {
            $this->order->payment->update([
                'status' => 'refunded'
            ]);
        }

        session()->flash('success', 'Order has been cancelled successfully.');
        $this->dispatch('refreshOrder');
    }

    public function getEstimatedReadyTime()
    {
        if (!$this->order->pickup_time || !$this->order->estimated_wait_time) {
            return null;
        }

        return $this->order->pickup_time->copy()->addMinutes($this->order->estimated_wait_time);
    }

    // Computed property for status color
    public function getStatusColorProperty()
    {
        if (!$this->order) return 'bg-gray-100 text-gray-800';

        return match($this->order->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'preparing' => 'bg-orange-100 text-orange-800',
            'ready' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Computed property for status icon
    public function getStatusIconProperty()
    {
        if (!$this->order) return '📋';

        return match($this->order->status) {
            'pending' => '⏳',
            'confirmed' => '✅',
            'preparing' => '👨‍🍳',
            'ready' => '📦',
            'completed' => '🎉',
            'cancelled' => '❌',
            default => '📋'
        };
    }

    // Helper methods for other statuses (like in timeline)
    public function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'preparing' => 'bg-orange-100 text-orange-800',
            'ready' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
// In App\Livewire\OrderDetail.php
    public function getImageUrl($imageUrl)
    {
        if (!$imageUrl) {
            return asset('images/default-meal.jpg');
        }

        // If it's already a full URL
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // If it's a local storage path
        return asset('storage/' . $imageUrl);
    }
    public function getStatusIcon($status)
    {
        return match($status) {
            'pending' => '⏳',
            'confirmed' => '✅',
            'preparing' => '👨‍🍳',
            'ready' => '📦',
            'completed' => '🎉',
            'cancelled' => '❌',
            default => '📋'
        };
    }

    public function render()
    {
        return view('livewire.order-detail')
            ->layout('components.layouts.app', [
                'title' => $this->order ? 'Order #' . $this->order->order_number . ' - Smart Canteen' : 'Order Details - Smart Canteen'
            ]);
    }
}
