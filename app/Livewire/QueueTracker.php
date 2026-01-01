<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Canteen;
use App\Models\Meal;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class QueueTracker extends Component
{
    public $canteenId;
    public $orders = [];
    public $queueStats = [];
    public $canteens;
    public $orderTimeEstimates = [];

    protected $listeners = ['orderUpdated' => 'refreshQueue'];

    public function mount($canteenId = null)
    {
        $this->canteenId = $canteenId;
        $this->canteens = Canteen::where('is_active', true)->get();
        $this->refreshQueue();
    }

    public function refreshQueue()
    {
        Log::info('RefreshQueue - Starting', ['canteen_id' => $this->canteenId]);

        // Reset data
        $this->orders = collect();
        $this->queueStats = [];
        $this->orderTimeEstimates = [];

        if ($this->canteenId) {
            $this->loadSingleCanteenQueue();
        } else {
            $this->loadAllCanteensOverview();
        }

        Log::info('RefreshQueue - Completed', [
            'canteen_id' => $this->canteenId,
            'orders_count' => count($this->orders),
            'queue_stats' => $this->queueStats
        ]);
    }

    private function loadSingleCanteenQueue()
    {
        $canteen = Canteen::find($this->canteenId);

        if (!$canteen) {
            Log::error('Canteen not found', ['canteen_id' => $this->canteenId]);
            $this->setEmptyQueueStats();
            return;
        }

        // METHOD 1: Use whereHas (recommended)
        $ordersQuery = Order::whereHas('meal', function($query) use ($canteen) {
            $query->where('canteen_id', $canteen->id);
        })
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with(['user', 'meal.canteen'])
            ->orderBy('created_at', 'asc');

        $this->orders = $ordersQuery->get();

        Log::info('Single Canteen Query Results', [
            'canteen_id' => $this->canteenId,
            'canteen_name' => $canteen->name,
            'orders_found' => $this->orders->count(),
            'order_ids' => $this->orders->pluck('id'),
            'sql_query' => $ordersQuery->toSql() // For debugging the actual SQL
        ]);

        if ($this->orders->count() > 0) {
            $this->calculateOrderTimeEstimates();
            $this->queueStats = $this->calculateQueueStats();
        } else {
            $this->setEmptyQueueStats();
        }
    }

    private function loadAllCanteensOverview()
    {
        foreach ($this->canteens as $canteen) {
            $orderCount = Order::whereHas('meal', function($query) use ($canteen) {
                $query->where('canteen_id', $canteen->id);
            })
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
                ->count();

            $waitTime = $this->calculateCanteenWaitTime($canteen->id);

            $this->queueStats[$canteen->id] = [
                'name' => $canteen->name,
                'queue_count' => $orderCount,
                'wait_time' => $waitTime,
                'is_open' => $canteen->is_open ?? true,
            ];
        }
    }

    private function setEmptyQueueStats()
    {
        $this->queueStats = [
            'total_in_queue' => 0,
            'estimated_wait_time' => 0,
            'current_serving' => 0,
            'orders_preparing' => 0,
            'orders_ready' => 0,
            'orders_pending' => 0,
            'orders_confirmed' => 0,
        ];
    }

    private function calculateCanteenWaitTime($canteenId)
    {
        $orders = Order::whereHas('meal', function($query) use ($canteenId) {
            $query->where('canteen_id', $canteenId);
        })
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with('meal')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            return 0;
        }

        $totalTime = 0;
        foreach ($orders as $order) {
            $prepTime = $order->meal->preparation_time ?? 10;

            if ($order->status === 'ready') {
                $orderTime = 0;
            } elseif ($order->status === 'preparing') {
                $orderTime = max(1, ceil($prepTime * 0.4));
            } else {
                $orderTime = $prepTime;
            }

            $totalTime += $orderTime;
        }

        return $totalTime;
    }

    private function calculateQueueStats()
    {
        $totalWaitTime = 0;
        $currentTime = 0;

        foreach ($this->orders as $order) {
            $prepTime = $order->meal->preparation_time ?? 10;

            if ($order->status === 'ready') {
                $orderWaitTime = 0;
            } elseif ($order->status === 'preparing') {
                $orderWaitTime = max(1, ceil($prepTime * 0.4));
            } else {
                $orderWaitTime = $prepTime;
            }

            $currentTime += $orderWaitTime;
            $totalWaitTime = $currentTime;
        }

        $currentServing = $this->orders->where('status', 'preparing')->first();
        if (!$currentServing) {
            $currentServing = $this->orders->where('status', 'ready')->first();
        }
        if (!$currentServing) {
            $currentServing = $this->orders->first();
        }

        return [
            'total_in_queue' => $this->orders->count(),
            'estimated_wait_time' => $totalWaitTime,
            'current_serving' => $currentServing ? ($currentServing->queue_number ?? '#' . $currentServing->id) : 'None',
            'orders_preparing' => $this->orders->where('status', 'preparing')->count(),
            'orders_ready' => $this->orders->where('status', 'ready')->count(),
            'orders_pending' => $this->orders->where('status', 'pending')->count(),
            'orders_confirmed' => $this->orders->where('status', 'confirmed')->count(),
        ];
    }

    private function calculateOrderTimeEstimates()
    {
        $currentTimeEstimate = 0;

        foreach ($this->orders as $order) {
            $prepTime = $order->meal->preparation_time ?? 10;

            if ($order->status === 'ready') {
                $minutesLeft = 0;
                $message = 'Ready for pickup';
            } elseif ($order->status === 'preparing') {
                $minutesLeft = max(1, ceil($prepTime * 0.4));
                $currentTimeEstimate += $minutesLeft;
                $message = "Ready in {$minutesLeft} min";
            } else {
                $currentTimeEstimate += $prepTime;
                $minutesLeft = $currentTimeEstimate;
                $message = "Est. {$minutesLeft} min";
            }

            $this->orderTimeEstimates[$order->id] = [
                'minutes_left' => $minutesLeft,
                'status' => $order->status,
                'message' => $message
            ];
        }
    }

    public function getTimeEstimate($orderId)
    {
        return $this->orderTimeEstimates[$orderId] ?? [
            'minutes_left' => 0,
            'status' => 'unknown',
            'message' => 'Estimating...'
        ];
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = Order::find($orderId);
        if ($order && in_array($status, ['preparing', 'ready', 'completed'])) {
            $order->update(['status' => $status]);
            $this->refreshQueue();
            $this->dispatch('orderUpdated');
            session()->flash('message', 'Order status updated successfully!');
        }
    }

    // Force refresh method for testing
    public function forceRefresh()
    {
        $this->refreshQueue();
        session()->flash('message', 'Queue forcefully refreshed!');
    }

    public function debugOrders()
    {
        if ($this->canteenId) {
            $canteen = Canteen::find($this->canteenId);

            $debugOrders = Order::whereHas('meal', function($query) use ($canteen) {
                $query->where('canteen_id', $canteen->id);
            })
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
                ->with(['user', 'meal.canteen'])
                ->orderBy('created_at', 'asc')
                ->get();

            return [
                'canteen_name' => $canteen->name ?? 'Unknown',
                'meal_ids' => $canteen ? $canteen->meals()->pluck('id') : [],
                'debug_orders_found' => $debugOrders->pluck('id'),
                'debug_orders_count' => $debugOrders->count(),
                'debug_order_details' => $debugOrders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'meal' => $order->meal->name ?? 'Unknown',
                        'status' => $order->status,
                        'queue_number' => $order->queue_number,
                        'prep_time' => $order->meal->preparation_time ?? 10,
                    ];
                })->toArray(),
                'current_component_orders' => $this->orders->pluck('id'),
                'current_component_orders_count' => $this->orders->count(),
                'queue_stats' => $this->queueStats
            ];
        }

        return [];
    }

    public function render()
    {
        // Log final render state
        Log::info('QueueTracker Render', [
            'canteen_id' => $this->canteenId,
            'orders_count' => count($this->orders),
            'has_orders' => !$this->orders->isEmpty(),
            'queue_stats' => $this->queueStats
        ]);

        return view('livewire.queue-tracker');
    }
}
