<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\Meal;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    public function run(): void
    {
        $canteens = Canteen::all();

        $meals = [
            // Main Campus Canteen Meals
            [
                'name' => 'Nasi Lemak Ayam',
                'description' => 'Traditional Malaysian coconut rice served with crispy fried chicken, sambal, fried anchovies, peanuts, and boiled egg.',
                'price' => 6.50,
                'category' => 'Malaysian',
                'nutritional_info' => json_encode([
                    'calories' => '650 kcal',
                    'protein' => '25g',
                    'carbs' => '75g',
                    'fat' => '28g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 15,
                'canteen_id' => $canteens[0]->id,
            ],
            [
                'name' => 'Mee Goreng Mamak',
                'description' => 'Spicy fried noodles with chicken, shrimp, tofu, and vegetables in authentic Mamak style.',
                'price' => 5.50,
                'category' => 'Malaysian',
                'nutritional_info' => json_encode([
                    'calories' => '580 kcal',
                    'protein' => '20g',
                    'carbs' => '85g',
                    'fat' => '18g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 12,
                'canteen_id' => $canteens[0]->id,
            ],
            [
                'name' => 'Chicken Chop',
                'description' => 'Grilled chicken breast served with black pepper sauce, mashed potatoes, and mixed vegetables.',
                'price' => 8.90,
                'category' => 'Western',
                'nutritional_info' => json_encode([
                    'calories' => '720 kcal',
                    'protein' => '35g',
                    'carbs' => '45g',
                    'fat' => '42g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 20,
                'canteen_id' => $canteens[0]->id,
            ],
            [
                'name' => 'Roti Canai with Dhal',
                'description' => 'Flaky flatbread served with lentil curry and chicken curry on the side.',
                'price' => 3.50,
                'category' => 'Indian',
                'nutritional_info' => json_encode([
                    'calories' => '420 kcal',
                    'protein' => '12g',
                    'carbs' => '55g',
                    'fat' => '16g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 8,
                'canteen_id' => $canteens[0]->id,
            ],

            // Engineering Faculty Canteen Meals
            [
                'name' => 'Nasi Goreng Kampung',
                'description' => 'Traditional village-style fried rice with anchovies, shrimp paste, and vegetables.',
                'price' => 5.00,
                'category' => 'Malaysian',
                'nutritional_info' => json_encode([
                    'calories' => '520 kcal',
                    'protein' => '15g',
                    'carbs' => '78g',
                    'fat' => '16g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 10,
                'canteen_id' => $canteens[1]->id,
            ],
            [
                'name' => 'Laksa Johor',
                'description' => 'Spicy noodle soup in rich fish broth with mackerel, vegetables, and herbs.',
                'price' => 6.80,
                'category' => 'Malaysian',
                'nutritional_info' => json_encode([
                    'calories' => '480 kcal',
                    'protein' => '22g',
                    'carbs' => '65g',
                    'fat' => '15g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 15,
                'canteen_id' => $canteens[1]->id,
            ],
            [
                'name' => 'Biryani Rice with Chicken',
                'description' => 'Fragrant basmati rice cooked with spices, served with tender chicken curry.',
                'price' => 7.50,
                'category' => 'Indian',
                'nutritional_info' => json_encode([
                    'calories' => '680 kcal',
                    'protein' => '28g',
                    'carbs' => '85g',
                    'fat' => '22g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 18,
                'canteen_id' => $canteens[1]->id,
            ],

            // Computer Science Cafe Meals
            [
                'name' => 'Grilled Chicken Salad',
                'description' => 'Fresh mixed greens with grilled chicken breast, cherry tomatoes, and balsamic dressing.',
                'price' => 9.50,
                'category' => 'Western',
                'nutritional_info' => json_encode([
                    'calories' => '320 kcal',
                    'protein' => '30g',
                    'carbs' => '12g',
                    'fat' => '16g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 10,
                'canteen_id' => $canteens[2]->id,
            ],
            [
                'name' => 'Beef Burger Deluxe',
                'description' => 'Juicy beef patty with cheese, lettuce, tomato, and special sauce in brioche bun.',
                'price' => 8.00,
                'category' => 'Western',
                'nutritional_info' => json_encode([
                    'calories' => '650 kcal',
                    'protein' => '32g',
                    'carbs' => '45g',
                    'fat' => '35g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 12,
                'canteen_id' => $canteens[2]->id,
            ],
            [
                'name' => 'Carbonara Pasta',
                'description' => 'Creamy pasta with bacon, mushrooms, and parmesan cheese.',
                'price' => 7.80,
                'category' => 'Western',
                'nutritional_info' => json_encode([
                    'calories' => '580 kcal',
                    'protein' => '25g',
                    'carbs' => '65g',
                    'fat' => '24g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 15,
                'canteen_id' => $canteens[2]->id,
            ],
            [
                'name' => 'Fish and Chips',
                'description' => 'Crispy battered fish served with thick-cut fries and tartar sauce.',
                'price' => 9.00,
                'category' => 'Western',
                'nutritional_info' => json_encode([
                    'calories' => '720 kcal',
                    'protein' => '28g',
                    'carbs' => '68g',
                    'fat' => '35g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 16,
                'canteen_id' => $canteens[2]->id,
            ],
            [
                'name' => 'Chicken Wrap',
                'description' => 'Grilled chicken strips with lettuce, tomatoes, and mayo in whole wheat wrap.',
                'price' => 6.50,
                'category' => 'Western',
                'nutritional_info' => json_encode([
                    'calories' => '380 kcal',
                    'protein' => '24g',
                    'carbs' => '35g',
                    'fat' => '16g'
                ]),
                'is_available' => true,
                'image_url' => null,
                'preparation_time' => 8,
                'canteen_id' => $canteens[2]->id,
            ],
        ];

        foreach ($meals as $meal) {
            Meal::create($meal);
        }

        $this->command->info('Meals seeded successfully!');
    }
}
