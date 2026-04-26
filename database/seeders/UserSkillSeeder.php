<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSkillSeeder extends Seeder
{
    public function run(): void
    {
        $freelancers = User::where('type', 'freelancer')->get();
        $skills = Skill::all();
        
        foreach ($freelancers as $freelancer) {
            $randomSkills = $skills->random(rand(3, 8));
            
            foreach ($randomSkills as $skill) {
                DB::table('skill_user')->insert([
                    'user_id' => $freelancer->id,
                    'skill_id' => $skill->id,
                    'years_of_experience' => rand(1, 10),
                ]);
            }
        }
    }
}