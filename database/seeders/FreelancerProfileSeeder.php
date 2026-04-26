<?php

namespace Database\Seeders;

use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Database\Seeder;

class FreelancerProfileSeeder extends Seeder
{
    public function run(): void
    {
        $freelancerUsers = User::where('type', 'freelancer')->get();
        
        $profileData = [
            2 => ['bio' => 'Senior Laravel & Vue.js developer with 5+ years of experience.', 'hourly_rate' => 45, 'availability' => 'available', 'verified' => true],
            3 => ['bio' => 'Full-stack developer specializing in React and Node.js.', 'hourly_rate' => 50, 'availability' => 'available', 'verified' => true],
            4 => ['bio' => 'UI/UX Designer with 4 years of experience.', 'hourly_rate' => 35, 'availability' => 'busy', 'verified' => true],
            5 => ['bio' => 'Junior developer looking for opportunities', 'hourly_rate' => 20, 'availability' => 'available', 'verified' => false],
            6 => ['bio' => 'Python & Django expert.', 'hourly_rate' => 55, 'availability' => 'available', 'verified' => true],
            7 => ['bio' => 'Mobile app developer (Flutter & React Native)', 'hourly_rate' => 40, 'availability' => 'available', 'verified' => false],
            8 => ['bio' => 'WordPress & Shopify expert.', 'hourly_rate' => 30, 'availability' => 'available', 'verified' => true],
            9 => ['bio' => 'DevOps Engineer | Cloud Architect', 'hourly_rate' => 70, 'availability' => 'busy', 'verified' => true],
            10 => ['bio' => 'Content writer and SEO specialist', 'hourly_rate' => 25, 'availability' => 'available', 'verified' => false],
            11 => ['bio' => 'Senior QA Engineer', 'hourly_rate' => 40, 'availability' => 'available', 'verified' => true],
            12 => ['bio' => 'Digital Marketing Specialist', 'hourly_rate' => 35, 'availability' => 'available', 'verified' => true],
            13 => ['bio' => 'Data Entry Specialist | Virtual Assistant', 'hourly_rate' => 15, 'availability' => 'available', 'verified' => false],
        ];
        
        foreach ($freelancerUsers as $user) {
            $data = $profileData[$user->id] ?? [
                'bio' => 'Experienced freelancer ready to help with your projects.',
                'hourly_rate' => rand(20, 80),
                'availability' => ['available', 'busy', 'not_available'][rand(0, 2)],
                'verified' => rand(0, 1) === 1,
            ];
            
            Freelancer::create([
                'user_id' => $user->id,
                'bio' => $data['bio'],
                'hourly_rate' => $data['hourly_rate'],
                'availability' => $data['availability'],
            ]);
        }
    }
}