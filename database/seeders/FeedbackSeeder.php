<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $completedOrders = Order::where('status', 'completed')
            ->with('user', 'meal')
            ->get();

        $feedbackCategories = [
            ['food_quality', 'service'],
            ['food_quality', 'taste', 'portion_size'],
            ['service', 'waiting_time'],
            ['food_quality', 'value', 'cleanliness'],
            ['taste', 'presentation'],
        ];

        foreach ($completedOrders as $order) {
            // Only add feedback to some orders
            if (rand(0, 1)) {
                Feedback::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'meal_id' => $order->meal_id,
                    'rating' => rand(3, 5),
                    'comment' => $this->getRandomComment(),
                    'categories' => $feedbackCategories[array_rand($feedbackCategories)],
                    'is_approved' => true,
                    'created_at' => $order->created_at->addHours(1),
                ]);
            }
        }

        $this->command->info('Sample feedback seeded successfully!');
    }

    private function getRandomComment(): string
    {
        $comments = [
            'Very delicious and worth the price!',
            'Fast service and good quality food.',
            'The portion was generous and tasty.',
            'Will definitely order again!',
            'Good value for money.',
            'The food was fresh and well-prepared.',
            'Excellent service, friendly staff.',
            'Tasty but could use more seasoning.',
            'Quick preparation time, impressive!',
            'One of my favorite meals on campus.',
        ];

        return $comments[array_rand($comments)];
    }
}
