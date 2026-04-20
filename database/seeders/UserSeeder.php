<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'first_name' => 'Samer', 
                'last_name'  => 'Client', 
                'email'      => 'client1@test.com', 
                'password'   => bcrypt('password'), 
                'type'       => 'client', 
                'city_id'    => 1, 
                'is_verified'=> true
            ],
            [
                'first_name' => 'Layla', 
                'last_name'  => 'Agency', 
                'email'      => 'client2@test.com', 
                'password'   => bcrypt('password'), 
                'type'       => 'client', 
                'city_id'    => 3, 
                'is_verified'=> true
            ],
            [
                'first_name' => 'Mohammed', 
                'last_name'  => 'Developer', 
                'email'      => 'freelancer1@test.com', 
                'password'   => bcrypt('password'), 
                'type'       => 'freelancer', 
                'city_id'    => 1, 
                'is_verified'=> true
            ],
            [
                'first_name' => 'Nour', 
                'last_name'  => 'Designer', 
                'email'      => 'freelancer2@test.com', 
                'password'   => bcrypt('password'), 
                'type'       => 'freelancer', 
                'city_id'    => 2, 
                'is_verified'=> false
            ],
        ]);
    }
}