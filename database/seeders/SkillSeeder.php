<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Laravel', 'PHP', 'Vue.js', 'React', 'Angular', 'Node.js', 'Python', 
            'Django', 'Flask', 'Ruby on Rails', 'Java', 'Spring Boot', 'C#', '.NET',
            'Flutter', 'React Native', 'Swift', 'Kotlin', 'UI/UX Design', 'Figma',
            'Adobe XD', 'Photoshop', 'Illustrator', 'WordPress', 'Shopify', 'Magento',
            'SEO', 'Digital Marketing', 'Social Media Management', 'Content Writing',
            'Copywriting', 'Translation', 'Data Entry', 'Virtual Assistant', 'Customer Service',
            'Project Management', 'Agile', 'Scrum', 'DevOps', 'Docker', 'Kubernetes',
            'AWS', 'Azure', 'Google Cloud', 'MySQL', 'PostgreSQL', 'MongoDB', 'Redis',
            'Cyber Security', 'Network Administration', 'Technical Support', 'QA Testing',
        ];
        
        foreach ($skills as $skill) {
            Skill::create(['name' => $skill]);
        }
    }
}