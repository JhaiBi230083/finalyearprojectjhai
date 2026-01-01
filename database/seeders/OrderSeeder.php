<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Meal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $meals = Meal::all();

        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];
        $paymentMethods = ['e-wallet', 'credit_card', 'online_banking'];

        for ($i = 0; $i < 50; $i++) {
            $student = $students->random();
            $meal = $meals->random();
            $status = $statuses[array_rand($statuses)];

            $order = Order::create([
                'user_id' => $student->id,
                'meal_id' => $meal->id,
                'quantity' => rand(1, 3),
                'total_amount' => $meal->price * rand(1, 3),
                'status' => $status,
                'pickup_time' => Carbon::now()->addMinutes(rand(30, 120)),
                'queue_number' => $status !== 'completed' ? null : rand(1, 20),
                'estimated_wait_time' => $meal->preparation_time,
                'special_instructions' => rand(0, 1) ? 'Less spicy please' : null,
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);

            // Create payment for completed orders
            if (in_array($status, ['completed', 'ready', 'preparing'])) {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'transaction_id' => 'TXN' . now()->format('YmdHis') . $i,
                    'amount' => $order->total_amount,
                    'status' => 'success',
                    'created_at' => $order->created_at,
                ]);
            }

            // Assign queue number for active orders
            if (in_array($status, ['confirmed', 'preparing', 'ready'])) {
                $order->update([
                    'queue_number' => rand(1, 15)
                ]);
            }
        }

        $this->command->info('Sample orders seeded successfully!');
    }
}
