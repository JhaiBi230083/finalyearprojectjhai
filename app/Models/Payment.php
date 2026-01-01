<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Methods
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
                'status' => 'success', // Changed from 'completed' to 'success'
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
}
