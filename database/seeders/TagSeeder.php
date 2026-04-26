<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Web Development', 'Mobile App', 'E-commerce', 'CMS', 'CRM', 'ERP',
            'Design', 'Logo Design', 'Branding', 'Marketing', 'SEO', 'Social Media',
            'Writing', 'Translation', 'Data Entry', 'Virtual Assistant', 'IT & Networking',
            'Security', 'DevOps', 'Cloud Computing', 'AI & Machine Learning', 'Blockchain',
            'Game Development', 'AR/VR', 'IoT', 'Robotics', 'Consulting', 'Training',
        ];
        
        foreach ($tags as $tag) {
            Tag::create(['name' => $tag]);
        }
    }
}