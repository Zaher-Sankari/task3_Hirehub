<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class FreelancerProfileSeeder extends Seeder
{
    public function run(): void
    {
        $freelancers = User::where('type', 'freelancer')->get();
        $php = Skill::where('name', 'PHP')->first();
        $js  = Skill::where('name', 'JavaScript')->first();
        $fig = Skill::where('name', 'Figma')->first();

        // Profile for Mohammed (Freelancer 1)
        $f1 = $freelancers->first();
        $profile1 = $f1->freelancerProfile()->create([
            'bio' => 'Expert Back-end Developer specialized in Laravel and APIs.',
            'hourly_rate' => 25.00,
            'availability' => 'available',
        ]);
        $profile1->skills()->attach([
            $php->id => ['years_of_experience' => 5], 
            $js->id  => ['years_of_experience' => 3]
        ]);

        // Profile for Nour (Freelancer 2)
        $f2 = $freelancers->skip(1)->first();
        $profile2 = $f2->freelancerProfile()->create([
            'bio' => 'Creative UI/UX Designer and Mobile App Specialist.',
            'hourly_rate' => 18.50,
            'availability' => 'busy',
        ]);
        $profile2->skills()->attach([
            $fig->id => ['years_of_experience' => 4]
        ]);
    }
}