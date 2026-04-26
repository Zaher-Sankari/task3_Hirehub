<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'HireHub',
            'email' => 'admin@hirehub.com',
            'password' => Hash::make('password123'),
            'type' => 'client',
            'city_id' => 1,
        ]);
        
        // Clients
        $clients = [
            ['first_name' => 'Ahmed', 'last_name' => 'Alsharif', 'email' => 'ahmed@example.com', 'city_id' => 5],
            ['first_name' => 'Mohammed', 'last_name' => 'AlRashid', 'email' => 'mohammed@example.com', 'city_id' => 10],
            ['first_name' => 'Fatima', 'last_name' => 'AlZahrani', 'email' => 'fatima@example.com', 'city_id' => 6],
            ['first_name' => 'Omar', 'last_name' => 'Hassan', 'email' => 'omar@example.com', 'city_id' => 1],
            ['first_name' => 'Layla', 'last_name' => 'Khalid', 'email' => 'layla@example.com', 'city_id' => 11],
            ['first_name' => 'Youssef', 'last_name' => 'Ibrahim', 'email' => 'youssef@example.com', 'city_id' => 13],
            ['first_name' => 'Nour', 'last_name' => 'Said', 'email' => 'nour@example.com', 'city_id' => 2],
            ['first_name' => 'Hassan', 'last_name' => 'Ali', 'email' => 'hassan@example.com', 'city_id' => 16],
        ];
        
        foreach ($clients as $client) {
            User::create([
                'first_name' => $client['first_name'],
                'last_name' => $client['last_name'],
                'email' => $client['email'],
                'password' => Hash::make('password123'),
                'type' => 'client',
                'city_id' => $client['city_id'],
            ]);
        }
        
        // Freelancers
        $freelancers = [
            ['first_name' => 'Sara', 'last_name' => 'Mahmoud', 'email' => 'sara@example.com', 'city_id' => 1],
            ['first_name' => 'Khaled', 'last_name' => 'Ahmed', 'email' => 'khaled@example.com', 'city_id' => 5],
            ['first_name' => 'Mona', 'last_name' => 'Lotfy', 'email' => 'mona@example.com', 'city_id' => 6],
            ['first_name' => 'Ali', 'last_name' => 'Reda', 'email' => 'ali@example.com', 'city_id' => 10],
            ['first_name' => 'Rana', 'last_name' => 'Tarek', 'email' => 'rana@example.com', 'city_id' => 11],
            ['first_name' => 'Hany', 'last_name' => 'Samir', 'email' => 'hany@example.com', 'city_id' => 13],
            ['first_name' => 'Dina', 'last_name' => 'Walid', 'email' => 'dina@example.com', 'city_id' => 2],
            ['first_name' => 'Amr', 'last_name' => 'Kamel', 'email' => 'amr@example.com', 'city_id' => 16],
            ['first_name' => 'Salma', 'last_name' => 'Youssef', 'email' => 'salma@example.com', 'city_id' => 3],
            ['first_name' => 'Mahmoud', 'last_name' => 'Hassan', 'email' => 'mahmoud@example.com', 'city_id' => 7],
            ['first_name' => 'Nadia', 'last_name' => 'Fouad', 'email' => 'nadia@example.com', 'city_id' => 14],
            ['first_name' => 'Tamer', 'last_name' => 'Eid', 'email' => 'tamer@example.com', 'city_id' => 8],
        ];
        
        foreach ($freelancers as $freelancer) {
            User::create([
                'first_name' => $freelancer['first_name'],
                'last_name' => $freelancer['last_name'],
                'email' => $freelancer['email'],
                'password' => Hash::make('password123'),
                'type' => 'freelancer',
                'city_id' => $freelancer['city_id'],
            ]);
        }
    }
}