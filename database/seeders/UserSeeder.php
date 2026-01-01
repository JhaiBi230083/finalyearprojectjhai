<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@smartcanteen.edu.my',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone_number' => '012-3456789',
            'faculty' => 'Administration',
        ]);

        // Vendor Users
        $vendors = [
            [
                'name' => 'Ali Ahmad',
                'email' => 'ali.ahmad@canteen.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'phone_number' => '012-1111111',
                'faculty' => 'Food Services',
            ],
            [
                'name' => 'Siti Fatimah',
                'email' => 'siti.fatimah@canteen.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'phone_number' => '012-2222222',
                'faculty' => 'Food Services',
            ],
            [
                'name' => 'Raj Kumar',
                'email' => 'raj.kumar@canteen.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'phone_number' => '012-3333333',
                'faculty' => 'Food Services',
            ],
        ];

        foreach ($vendors as $vendor) {
            User::create($vendor);
        }

        // Staff Users
        $staff = [
            [
                'name' => 'Dr. Lee Chen',
                'email' => 'lee.chen@uthm.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone_number' => '012-4444444',
                'faculty' => 'Faculty of Computer Science',
            ],
            [
                'name' => 'Prof. Dr. Aminah',
                'email' => 'aminah@uthm.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone_number' => '012-5555555',
                'faculty' => 'Faculty of Engineering',
            ],
        ];

        foreach ($staff as $staffMember) {
            User::create($staffMember);
        }

        // Student Users
        $students = [
            [
                'name' => 'Ahmad bin Ismail',
                'email' => 'bi230001@student.uthm.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone_number' => '012-6666666',
                'faculty' => 'Faculty of Computer Science',
                'matric_number' => 'BI230001',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'bi230002@student.uthm.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone_number' => '012-7777777',
                'faculty' => 'Faculty of Computer Science',
                'matric_number' => 'BI230002',
            ],
            [
                'name' => 'Wei Jian',
                'email' => 'bi230003@student.uthm.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone_number' => '012-8888888',
                'faculty' => 'Faculty of Engineering',
                'matric_number' => 'BI230003',
            ],
            [
                'name' => 'Priya a/p Raj',
                'email' => 'bi230004@student.uthm.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone_number' => '012-9999999',
                'faculty' => 'Faculty of Engineering',
                'matric_number' => 'BI230004',
            ],
            [
                'name' => 'Muthu a/l Kumar',
                'email' => 'bi230005@student.uthm.edu.my',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone_number' => '012-1010101',
                'faculty' => 'Faculty of Technology',
                'matric_number' => 'BI230005',
            ],
        ];

        foreach ($students as $student) {
            User::create($student);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin Login: admin@smartcanteen.edu.my / password123');
        $this->command->info('Student Login: bi230001@student.uthm.edu.my / password123');
    }
}
