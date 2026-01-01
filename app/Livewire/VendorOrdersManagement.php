<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Canteen;
use App\Models\Notification as NotificationModel;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class VendorOrdersManagement extends Component
{
    use WithPagination;

    public $canteen;
    public $filterStatus = '';
    public $search = '';
    public $selectedOrder = null;
    public $showOrderModal = false;
    public $exportStartDate;
    public $exportEndDate;
    public $exportStatus = '';

    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        // Get vendor's canteen
        $this->canteen = Canteen::where('vendor_id', Auth::id())->first();

        if (!$this->canteen) {
            session()->flash('error', 'No canteen assigned to your account.');
        }

        // Set default export dates (last 30 days)
        $this->exportStartDate = now()->subDays(30)->format('Y-m-d');
        $this->exportEndDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $orders = Order::with(['user', 'meal', 'payment'])
            ->whereHas('meal', function($query) {
                $query->where('canteen_id', $this->canteen?->id);
            })
            ->when($this->filterStatus, function($query) {
                return $query->where('status', $this->filterStatus);
            })
            ->when($this->search, function($query) {
                return $query->where(function($q) {
                    $q->where('order_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function($q) {
                            $q->where('name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('meal', function($q) {
                            $q->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Order statistics
        $stats = [
            'total' => Order::whereHas('meal', function($query) {
                $query->where('canteen_id', $this->canteen?->id);
            })->count(),
            'pending' => Order::whereHas('meal', function($query) {
                $query->where('canteen_id', $this->canteen?->id);
            })->where('status', 'pending')->count(),
            'preparing' => Order::whereHas('meal', function($query) {
                $query->where('canteen_id', $this->canteen?->id);
            })->where('status', 'preparing')->count(),
            'ready' => Order::whereHas('meal', function($query) {
                $query->where('canteen_id', $this->canteen?->id);
            })->where('status', 'ready')->count(),
            'today' => Order::whereHas('meal', function($query) {
                $query->where('canteen_id', $this->canteen?->id);
            })->whereDate('created_at', today())->count(),
        ];

        return view('livewire.vendor-orders-management', compact('orders', 'stats'));
    }

    public function updateOrderStatus($orderId, $status)
    {
        // Load order with all necessary relationships
        $order = Order::with(['meal.canteen', 'user'])->findOrFail($orderId);

        // Verify order belongs to vendor's canteen
        if ($order->meal->canteen_id !== $this->canteen?->id) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            session()->flash('error', 'Invalid status.');
            return;
        }

        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        // Add timestamp for specific status changes and create notifications
        if ($status === 'preparing') {
            $order->update(['preparation_started_at' => now()]);
            NotificationModel::createOrderNotification($order, 'start_prep', Auth::user());
        } elseif ($status === 'ready') {
            $order->update(['ready_at' => now()]);
            NotificationModel::createOrderNotification($order, 'mark_ready', Auth::user());
        } elseif ($status === 'completed') {
            $order->update(['completed_at' => now()]);
            NotificationModel::createOrderNotification($order, 'complete', Auth::user());
        } elseif ($status === 'cancelled') {
            NotificationModel::createOrderNotification($order, 'cancel', Auth::user());
        }

        // Dispatch event for real-time updates with order number
        $this->dispatch('order-status-updated', [
            'orderId' => $orderId,
            'orderNumber' => $order->order_number,
            'status' => $status
        ]);

        // Use the order_number in the success message
        session()->flash('success', "Order #{$order->order_number} status updated to {$status}.");

        // If modal is open and it's the selected order, refresh it
        if ($this->showOrderModal && $this->selectedOrder && $this->selectedOrder->id === $orderId) {
            $this->selectedOrder = Order::with(['user', 'meal', 'payment'])->find($orderId);
        }
    }

    public function viewOrderDetails($orderId)
    {
        $this->selectedOrder = Order::with(['user', 'meal', 'payment'])
            ->whereHas('meal', function($query) {
                $query->where('canteen_id', $this->canteen?->id);
            })
            ->findOrFail($orderId);

        $this->showOrderModal = true;
    }

    public function closeOrderModal()
    {
        $this->showOrderModal = false;
        $this->selectedOrder = null;
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
            'preparing' => 'bg-orange-100 text-orange-800 border-orange-200',
            'ready' => 'bg-green-100 text-green-800 border-green-200',
            'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200'
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

    public function getActionButtons($status)
    {
        return match($status) {
            'pending' => [
                ['status' => 'confirmed', 'label' => 'Confirm', 'color' => 'blue'],
                ['status' => 'preparing', 'label' => 'Start Prep', 'color' => 'orange'],
                ['status' => 'cancelled', 'label' => 'Cancel', 'color' => 'red'],
            ],
            'confirmed' => [
                ['status' => 'preparing', 'label' => 'Start Prep', 'color' => 'orange'],
                ['status' => 'cancelled', 'label' => 'Cancel', 'color' => 'red'],
            ],
            'preparing' => [
                ['status' => 'ready', 'label' => 'Mark Ready', 'color' => 'green'],
            ],
            'ready' => [
                ['status' => 'completed', 'label' => 'Complete', 'color' => 'gray'],
            ],
            default => []
        };
    }

    public function calculatePreparationTime($order)
    {
        if (!$order->preparation_started_at) {
            return 'Not started';
        }

        $start = $order->preparation_started_at;
        $end = $order->ready_at ?? now();

        $diff = $start->diff($end);

        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        }

        return $diff->i . ' minutes';
    }

    public function exportOrders()
    {
        if (!$this->canteen) {
            session()->flash('error', 'No canteen assigned to your account.');
            return;
        }

        try {
            // Build query for export
            $exportQuery = Order::with(['user', 'meal', 'payment'])
                ->whereHas('meal', function($query) {
                    $query->where('canteen_id', $this->canteen->id);
                })
                ->when($this->exportStartDate, function($query) {
                    return $query->whereDate('created_at', '>=', $this->exportStartDate);
                })
                ->when($this->exportEndDate, function($query) {
                    return $query->whereDate('created_at', '<=', $this->exportEndDate);
                })
                ->when($this->exportStatus, function($query) {
                    return $query->where('status', $this->exportStatus);
                })
                ->orderBy('created_at', 'desc');

            $orders = $exportQuery->get();

            if ($orders->isEmpty()) {
                session()->flash('error', 'No orders found for the selected criteria.');
                return;
            }

            $fileName = 'orders_' . $this->canteen->name . '_' . now()->format('Y_m_d_His') . '.xlsx';

            return Excel::download(new OrdersExport($orders, $this->canteen->id), $fileName);

        } catch (\Exception $e) {
            session()->flash('error', 'Error exporting orders: ' . $e->getMessage());
        }
    }

    public function quickExport()
    {
        // Quick export without filters
        if (!$this->canteen) {
            session()->flash('error', 'No canteen assigned to your account.');
            return;
        }

        try {
            $orders = Order::with(['user', 'meal', 'payment'])
                ->whereHas('meal', function($query) {
                    $query->where('canteen_id', $this->canteen->id);
                })
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->get();

            if ($orders->isEmpty()) {
                session()->flash('error', 'No orders found for export.');
                return;
            }

            $fileName = 'orders_quick_export_' . now()->format('Y_m_d_His') . '.xlsx';

            return Excel::download(new OrdersExport($orders, $this->canteen->id), $fileName);

        } catch (\Exception $e) {
            session()->flash('error', 'Error exporting orders: ' . $e->getMessage());
        }
    }
}
