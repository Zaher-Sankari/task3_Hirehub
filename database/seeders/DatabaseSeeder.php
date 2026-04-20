<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
            TagSeeder::class, 
            SkillSeeder::class,
        ]);
        $this->call([
            UserSeeder::class,
            FreelancerProfileSeeder::class,
        ]);
        $this->call([
            ProjectSeeder::class, 
            BidSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}