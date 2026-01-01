<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Canteen;
use Illuminate\Support\Facades\Auth;

class StudentQueue extends Component
{
    public $canteens;
    public $selectedCanteenId = null;
    public $queueStats = [];
    public $myOrders = [];
    public $otherOrders = [];
    public $loading = true;

    public function mount()
    {
        $this->canteens = Canteen::where('is_active', true)->get();

        // Auto-select first canteen if available
        if ($this->canteens->count() > 0) {
            $this->selectedCanteenId = $this->canteens->first()->id;
        }

        $this->loadQueueData();
    }

    public function loadQueueData()
    {
        $this->loading = true;

        if ($this->selectedCanteenId) {
            $this->loadCanteenQueue();
            $this->loadMyOrders();
        }

        $this->loading = false;
    }

    private function loadCanteenQueue()
    {
        $canteen = Canteen::find($this->selectedCanteenId);

        if (!$canteen) {
            $this->queueStats = [];
            $this->otherOrders = [];
            return;
        }

        // Get all active orders for this canteen
        $orders = Order::whereHas('meal', function($query) {
            $query->where('canteen_id', $this->selectedCanteenId);
        })
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with(['user', 'meal'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate queue statistics
        $totalOrders = $orders->count();
        $pendingOrders = $orders->whereIn('status', ['pending', 'confirmed'])->count();
        $preparingOrders = $orders->where('status', 'preparing')->count();
        $readyOrders = $orders->where('status', 'ready')->count();

        // Calculate estimated wait times
        $estimatedWaitTime = $this->calculateEstimatedWaitTime($orders);
        $currentServing = $this->getCurrentServingOrder($orders);

        $this->queueStats = [
            'canteen_name' => $canteen->name,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'preparing_orders' => $preparingOrders,
            'ready_orders' => $readyOrders,
            'estimated_wait_time' => $estimatedWaitTime,
            'current_serving' => $currentServing,
            'is_open' => $canteen->isOpen(),
        ];

        // Separate user's orders from others
        $this->otherOrders = $orders->where('user_id', '!=', Auth::id())->values();
    }

    private function loadMyOrders()
    {
        $this->myOrders = Order::whereHas('meal', function($query) {
            $query->where('canteen_id', $this->selectedCanteenId);
        })
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with(['meal'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function calculateEstimatedWaitTime($orders)
    {
        if ($orders->isEmpty()) {
            return 0;
        }

        $totalWaitTime = 0;
        $currentTime = 0;

        foreach ($orders as $order) {
            $prepTime = $order->meal->preparation_time ?? 10;

            if ($order->status === 'ready') {
                $orderWaitTime = 0;
            } elseif ($order->status === 'preparing') {
                $orderWaitTime = max(1, ceil($prepTime * 0.4)); // 40% remaining
            } else {
                $orderWaitTime = $prepTime;
            }

            $currentTime += $orderWaitTime;
            $totalWaitTime = $currentTime;
        }

        return $totalWaitTime;
    }

    private function getCurrentServingOrder($orders)
    {
        // Find currently preparing order, or first order if none preparing
        $current = $orders->where('status', 'preparing')->first();
        if (!$current) {
            $current = $orders->where('status', 'ready')->first();
        }
        if (!$current) {
            $current = $orders->first();
        }

        return $current ? ($current->queue_number ?? '#' . $current->id) : 'None';
    }

    public function getQueuePosition($order)
    {
        if (!$this->selectedCanteenId) return null;

        $allOrders = Order::whereHas('meal', function($query) {
            $query->where('canteen_id', $this->selectedCanteenId);
        })
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->toArray();

        $position = array_search($order->id, $allOrders);
        return $position !== false ? $position + 1 : null;
    }

    public function getEstimatedReadyTime($order)
    {
        $position = $this->getQueuePosition($order);
        if (!$position) return 'Calculating...';

        $avgPrepTime = 10; // Average preparation time in minutes
        $estimatedMinutes = ($position - 1) * $avgPrepTime;

        if ($order->status === 'preparing') {
            $estimatedMinutes = max(5, ceil($estimatedMinutes * 0.5));
        } elseif ($order->status === 'ready') {
            return 'Ready Now';
        }

        return $estimatedMinutes > 0 ? "~{$estimatedMinutes} min" : 'Soon';
    }

    public function updatedSelectedCanteenId()
    {
        $this->loadQueueData();
    }

    // Auto-refresh every 30 seconds
    public function refreshQueue()
    {
        $this->loadQueueData();
    }

    public function render()
    {
        return view('livewire.student-queue')
            ->layout('components.layouts.app', [
                'title' => 'Queue Status - Smart Canteen'
            ]);
    }
}
