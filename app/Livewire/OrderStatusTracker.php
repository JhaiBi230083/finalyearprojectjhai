<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderStatusTracker extends Component
{
    public $orderId;
    public $order;
    public $loading = true;
    public $notFound = false;

    // Status timeline
    public $statusTimeline = [];

    protected $listeners = ['refreshOrder' => 'refreshOrderStatus'];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrder();
        $this->buildStatusTimeline();
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

    public function refreshOrderStatus()
    {
        $this->loadOrder();
        $this->buildStatusTimeline();
    }

    public function buildStatusTimeline()
    {
        if (!$this->order) return;

        $timeline = [];

        // Pending status (always present)
        $timeline[] = [
            'status' => 'pending',
            'icon' => '⏳',
            'title' => 'Order Placed',
            'description' => 'We have received your order',
            'time' => $this->order->created_at,
            'completed' => true, // Always completed once order is placed
            'current' => false,
        ];

        // Confirmed status
        $timeline[] = [
            'status' => 'confirmed',
            'icon' => '✅',
            'title' => 'Order Confirmed',
            'description' => 'Payment confirmed and order queued',
            'time' => $this->order->created_at->addMinutes(2), // Simulate confirmation time
            'completed' => in_array($this->order->status, ['confirmed', 'preparing', 'ready', 'completed']),
            'current' => $this->order->status === 'confirmed',
        ];

        // Preparing status
        $timeline[] = [
            'status' => 'preparing',
            'icon' => '👨‍🍳',
            'title' => 'Preparing your meal',
            'description' => 'Chef is cooking your delicious meal',
            'time' => $this->order->created_at->addMinutes(5), // Simulate preparation start time
            'completed' => in_array($this->order->status, ['preparing', 'ready', 'completed']),
            'current' => $this->order->status === 'preparing',
        ];

        // Ready status
        $timeline[] = [
            'status' => 'ready',
            'icon' => '📦',
            'title' => 'Ready for pickup',
            'description' => 'Your order is ready at the counter',
            'time' => $this->order->pickup_time ?? $this->order->created_at->addMinutes($this->order->estimated_wait_time ?? 20),
            'completed' => in_array($this->order->status, ['ready', 'completed']),
            'current' => $this->order->status === 'ready',
        ];

        // Completed status
        $timeline[] = [
            'status' => 'completed',
            'icon' => '🎉',
            'title' => 'Order completed',
            'description' => 'Enjoy your meal!',
            'time' => $this->order->updated_at->gt($this->order->created_at) ? $this->order->updated_at : null,
            'completed' => $this->order->status === 'completed',
            'current' => $this->order->status === 'completed',
        ];

        $this->statusTimeline = $timeline;
    }

    public function getCurrentStatusIndex()
    {
        $statusOrder = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
        return array_search($this->order->status, $statusOrder) ?? 0;
    }

    public function getProgressPercentage()
    {
        $totalSteps = 4; // pending -> confirmed -> preparing -> ready -> completed
        $currentStep = $this->getCurrentStatusIndex();
        return min(100, max(0, ($currentStep / $totalSteps) * 100));
    }

    // Auto-refresh for active orders
    public function checkForUpdates()
    {
        if ($this->order && !in_array($this->order->status, ['completed', 'cancelled'])) {
            $this->refreshOrderStatus();
        }
    }
// Add these methods to the OrderStatusTracker class

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

    public function getEstimatedReadyTime()
    {
        if (!$this->order->pickup_time || !$this->order->estimated_wait_time) {
            return null;
        }

        return $this->order->pickup_time->copy()->addMinutes($this->order->estimated_wait_time);
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
        $this->refreshOrderStatus();
    }
    public function render()
    {
        return view('livewire.order-status-tracker')
            ->layout('components.layouts.app', [
                'title' => $this->order ? 'Order #' . $this->order->order_number . ' - Status Tracker' : 'Order Status - Smart Canteen'
            ]);
    }
}
