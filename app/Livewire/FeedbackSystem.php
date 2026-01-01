<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class FeedbackSystem extends Component
{
    public $orderId;
    public $rating = 5;
    public $comment = '';
    public $categories = [];
    public $selectedOrder = null;

    protected $rules = [
        'orderId' => 'required|exists:orders,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
        'categories' => 'array',
    ];

    protected $queryString = ['orderId'];

    public function mount()
    {
        // If order ID is provided in query string, load the order
        if ($this->orderId) {
            $this->loadOrder($this->orderId);
        }
    }

    public function loadOrder($orderId)
    {
        $this->selectedOrder = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with('meal.canteen')
            ->first();

        if ($this->selectedOrder) {
            $this->orderId = $orderId;
        }
    }

    public function submitFeedback()
    {
        $this->validate();

        // Verify the order belongs to the user and is completed
        $order = Order::where('id', $this->orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->first();

        if (!$order) {
            session()->flash('error', 'Invalid order or order not completed.');
            return;
        }

        // Check if feedback already exists for this order
        $existingFeedback = Feedback::where('order_id', $this->orderId)->first();
        if ($existingFeedback) {
            session()->flash('error', 'Feedback already submitted for this order.');
            return;
        }

        // Create feedback
        Feedback::create([
            'user_id' => Auth::id(),
            'order_id' => $this->orderId,
            'meal_id' => $order->meal_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'categories' => $this->categories,
            'is_approved' => true,
        ]);

        // Reset form
        $this->reset(['rating', 'comment', 'categories', 'orderId', 'selectedOrder']);

        session()->flash('success', 'Thank you for your feedback!');
    }

    public function render()
    {
        $completedOrders = Auth::user()->orders()
            ->where('status', 'completed')
            ->with('meal.canteen')
            ->whereDoesntHave('feedback')
            ->orderBy('created_at', 'desc')
            ->get();

        $userFeedback = Auth::user()->feedback()
            ->with(['order.meal.canteen', 'meal'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = Auth::user()->feedback()->avg('rating') ?? 0;
        $totalFeedback = Auth::user()->feedback()->count();

        return view('livewire.feedback-system', compact('completedOrders', 'userFeedback', 'averageRating', 'totalFeedback'))
            ->layout('components.layouts.app', [
                'title' => 'Feedback - Smart Canteen'
            ]);
    }
}
