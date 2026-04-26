<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        $this->call([
            CountryCitySeeder::class,
            SkillSeeder::class,
            TagSeeder::class,
            UserSeeder::class,
            FreelancerProfileSeeder::class,
            ProjectSeeder::class,
            BidSeeder::class,
            ReviewSeeder::class,
            ProjectTagSeeder::class,
            UserSkillSeeder::class,
        ]);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('HireHub database seeded successfully!');
    }
}