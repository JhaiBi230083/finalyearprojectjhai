<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\User;
use Illuminate\Database\Seeder;

class CanteenSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = User::where('role', 'vendor')->get();

        $canteens = [
            [
                'name' => 'Main Campus Canteen',
                'description' => 'The main canteen located near the student center. Offers a wide variety of local Malaysian dishes.',
                'location' => 'Near Student Center, Main Campus',
                'opening_time' => '07:00:00',
                'closing_time' => '20:00:00',
                'is_active' => true,
                'vendor_id' => $vendors[0]->id,
            ],
            [
                'name' => 'Engineering Faculty Canteen',
                'description' => 'Popular canteen serving delicious and affordable meals for engineering students and staff.',
                'location' => 'Engineering Faculty Building, Block A',
                'opening_time' => '07:30:00',
                'closing_time' => '19:00:00',
                'is_active' => true,
                'vendor_id' => $vendors[1]->id,
            ],
            [
                'name' => 'Computer Science Cafe',
                'description' => 'Modern cafe with variety of western and local fusion dishes. Perfect for tech students.',
                'location' => 'FCSIT Building, Level 2',
                'opening_time' => '08:00:00',
                'closing_time' => '18:00:00',
                'is_active' => true,
                'vendor_id' => $vendors[2]->id,
            ],
            [
                'name' => 'Sports Complex Cafe',
                'description' => 'Healthy food options and beverages near the sports complex.',
                'location' => 'Sports Complex Building',
                'opening_time' => '06:00:00',
                'closing_time' => '22:00:00',
                'is_active' => false, // Temporarily closed
                'vendor_id' => $vendors[0]->id,
            ],
        ];

        foreach ($canteens as $canteen) {
            Canteen::create($canteen);
        }

        $this->command->info('Canteens seeded successfully!');
    }
}
