<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OrderHistory extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $search = '';

    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function render()
    {
        $orders = Auth::user()->orders()
            ->with(['meal.canteen', 'payment', 'feedback'])
            ->when($this->filterStatus, function ($query) {
                return $query->where('status', $this->filterStatus);
            })
            ->when($this->search, function ($query) {
                return $query->whereHas('meal', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.order-history', compact('orders'))
            ->layout('components.layouts.app', [
                'title' => 'My Orders - Smart Canteen'
            ]);
    }

    public function updateFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filterStatus = '';
        $this->search = '';
        $this->resetPage();
    }

    public function cancelOrder($orderId)
    {
        $order = Auth::user()->orders()->where('id', $orderId)->first();

        if ($order && in_array($order->status, ['pending', 'confirmed'])) {
            $order->update(['status' => 'cancelled']);
            session()->flash('success', 'Order cancelled successfully.');
        } else {
            session()->flash('error', 'Cannot cancel this order.');
        }
    }
// Add these methods to the OrderHistory class

// Helper methods for status display
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
    // Add this method to navigate to order details
    public function viewOrderDetails($orderId)
    {
        return redirect()->route('order.details', ['orderId' => $orderId]);
    }
}
