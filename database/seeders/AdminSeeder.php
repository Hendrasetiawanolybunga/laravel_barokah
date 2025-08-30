<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::updateOrCreate(
            ['email' => 'admin@barokah.com'],
            [
                'name' => 'Admin Barokah',
                'email' => 'admin@barokah.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_loyal' => false,
                'message' => null,
            ]
        );
    }
}
