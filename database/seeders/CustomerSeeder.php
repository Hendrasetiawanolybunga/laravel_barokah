<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define realistic job titles for customers
        $jobs = [
            'Mahasiswa',
            'Karyawan Swasta',
            'Wiraswasta',
            'PNS',
            'Guru',
            'Dokter',
            'Insinyur',
            'Desainer',
            'Marketing',
            'Programmer',
            'Akuntan',
            'Konsultan'
        ];

        // Define realistic addresses for customers
        $addresses = [
            'Jl. Merdeka No. 123, Jakarta',
            'Jl. Sudirman No. 45, Bandung',
            'Jl. Diponegoro No. 67, Surabaya',
            'Jl. Thamrin No. 89, Medan',
            'Jl. Gatot Subroto No. 101, Semarang',
            'Jl. Ahmad Yani No. 202, Yogyakarta',
            'Jl. Pahlawan No. 303, Makassar',
            'Jl. Veteran No. 404, Denpasar',
            'Jl. Asia Afrika No. 505, Bandar Lampung',
            'Jl. Imam Bonjol No. 606, Palembang',
            'Jl. Cendana No. 707, Balikpapan',
            'Jl. Kenanga No. 808, Manado',
            'Jl. Mangga No. 111, Malang',
            'Jl. Jeruk No. 222, Solo',
            'Jl. Anggrek No. 333, Batam',
            'Jl. Melati No. 444, Pontianak',
            'Jl. Mawar No. 555, Padang',
            'Jl. Dahlia No. 666, Samarinda',
            'Jl. Teratai No. 777, Banjarmasin',
            'Jl. Kamboja No. 888, Pekanbaru',
            'Jl. Bougenville No. 999, Jambi',
            'Jl. Sakura No. 1010, Mataram'
        ];

        // Create 22 customers (12 existing + 10 new)
        for ($i = 1; $i <= 22; $i++) {
            // Create a user with customer role
            $user = User::create([
                'name' => fake()->name(),
                'email' => 'customer' . $i . '@example.com',
                'password' => Hash::make('password123'), // Standard password for all customers
                'role' => 'customer',
                'is_loyal' => fake()->boolean(30), // 30% chance of being loyal
                'message' => fake()->boolean(20) ? fake()->sentence() : null, // 20% chance of having a message
                'remember_token' => Str::random(10),
            ]);

            // Create customer profile
            Customer::create([
                'user_id' => $user->id,
                'tgl_lahir' => fake()->dateTimeBetween('-60 years', '-18 years'), // Age between 18-60 years
                'alamat' => $addresses[$i - 1], // Use predefined addresses
                'pekerjaan' => $jobs[array_rand($jobs)], // Random job from the list
                'no_hp' => '08' . fake()->numberBetween(1000000000, 9999999999), // Indonesian phone number format
            ]);
        }
        
        echo "Created 22 customers.\n";
    }
}