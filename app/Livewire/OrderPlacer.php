<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Meal;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class OrderPlacer extends Component
{
    public $mealId;
    public $meal;
    public $quantity = 1;
    public $specialInstructions = '';
    public $pickupTime;
    public $showPaymentModal = false;
    public $showSuccessModal = false;
    public $paymentMethod = 'e-wallet';
    public $latestOrderId;

    protected $rules = [
        'quantity' => 'required|integer|min:1|max:10',
        'specialInstructions' => 'nullable|string|max:500',
        'pickupTime' => 'required|date|after:now',
    ];

    public function mount($mealId)
    {
        $this->mealId = $mealId;
        $this->loadMeal();
    }

    // In OrderPlacer.php - update the loadMeal method

    public function loadMeal()
    {
        if ($this->mealId) {
            $this->meal = Meal::with('canteen')->findOrFail($this->mealId);

            // Ensure nutritional_info is properly decoded
            if ($this->meal->nutritional_info && is_string($this->meal->nutritional_info)) {
                $this->meal->nutritional_info = json_decode($this->meal->nutritional_info, true);
            }

            // Fix image URL if needed
            if ($this->meal->image_url) {
                // This will be handled by the accessor now
            }

            $this->pickupTime = now()->addMinutes(30)->format('Y-m-d\TH:i');
        }
    }

    public function placeOrder()
    {
        $this->validate();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'meal_id' => $this->meal->id,
            'quantity' => $this->quantity,
            'total_amount' => $this->meal->price * $this->quantity,
            'special_instructions' => $this->specialInstructions,
            'pickup_time' => $this->pickupTime,
            'estimated_wait_time' => $this->meal->preparation_time,
        ]);

        $this->showPaymentModal = true;
        $this->latestOrderId = $order->id;
    }

    public function processPayment()
    {
        if (!$this->latestOrderId) {
            return;
        }

        $order = Order::find($this->latestOrderId);

        if ($order) {
            // Generate a unique transaction ID
            $transactionId = 'TXN' . now()->format('YmdHis') . rand(1000, 9999);

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $this->paymentMethod,
                'transaction_id' => $transactionId,
                'amount' => $order->total_amount,
                'status' => 'completed',
            ]);

            // Update order status
            $order->update([
                'status' => 'confirmed'
            ]);

            // Assign queue number and calculate wait time
            $order->assignQueueNumber();
            $order->estimated_wait_time = $order->calculateEstimatedWaitTime();
            $order->save();

            // Show success modal
            $this->showPaymentModal = false;
            $this->showSuccessModal = true;
        }
    }

    public function goToOrderHistory()
    {
        $this->showSuccessModal = false;
        return redirect()->route('order.history');
    }

    public function goToOrderDetails()
    {
        if ($this->latestOrderId) {
            $this->showSuccessModal = false;
            return redirect()->route('order.details', ['orderId' => $this->latestOrderId]);
        }
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        // Optionally redirect to menu or keep on current page
        return redirect()->route('menu.browse');
    }

    public function increment()
    {
        $this->quantity++;
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    // Helper method to get nutritional info as array
    public function getNutritionalInfoProperty()
    {
        if (!$this->meal || !$this->meal->nutritional_info) {
            return [];
        }

        if (is_array($this->meal->nutritional_info)) {
            return $this->meal->nutritional_info;
        }

        if (is_string($this->meal->nutritional_info)) {
            return json_decode($this->meal->nutritional_info, true) ?? [];
        }

        return [];
    }

    public function render()
    {
        return view('livewire.order-placer')
            ->layout('components.layouts.app', [
                'title' => $this->meal ? 'Order ' . $this->meal->name . ' - Smart Canteen' : 'Order Meal - Smart Canteen'
            ]);
    }
}
