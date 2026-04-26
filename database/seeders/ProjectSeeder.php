<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('type', 'client')->get();
        
        $projects = [
            ['title' => 'E-commerce Website with Laravel', 'description' => 'Need a full-featured e-commerce website with payment gateway integration.', 'budget_type' => 'fixed', 'budget' => 2500, 'status' => 'open'],
            ['title' => 'Mobile App UI/UX Design', 'description' => 'Design a modern mobile app for food delivery.', 'budget_type' => 'fixed', 'budget' => 800, 'status' => 'open'],
            ['title' => 'Laravel Backend API Development', 'description' => 'Build RESTful API for a real estate platform.', 'budget_type' => 'hourly', 'budget' => 45, 'status' => 'open'],
            ['title' => 'WordPress Website for Restaurant', 'description' => 'Create a WordPress website with online ordering.', 'budget_type' => 'fixed', 'budget' => 600, 'status' => 'in_progress'],
            ['title' => 'React Native Mobile App', 'description' => 'Develop a fitness tracking mobile app.', 'budget_type' => 'hourly', 'budget' => 50, 'status' => 'open'],
            ['title' => 'SEO Optimization for E-commerce', 'description' => 'Improve SEO for an existing e-commerce website.', 'budget_type' => 'fixed', 'budget' => 1200, 'status' => 'open'],
            ['title' => 'Social Media Management', 'description' => 'Manage social media accounts for a fashion brand.', 'budget_type' => 'hourly', 'budget' => 25, 'status' => 'closed'],
            ['title' => 'Python Web Scraping Script', 'description' => 'Build a web scraping script to extract product data.', 'budget_type' => 'fixed', 'budget' => 400, 'status' => 'open'],
            ['title' => 'Corporate Logo Design', 'description' => 'Design a professional logo for a tech startup.', 'budget_type' => 'fixed', 'budget' => 250, 'status' => 'open'],
            ['title' => 'DevOps Setup - AWS Deployment', 'description' => 'Set up CI/CD pipeline on AWS.', 'budget_type' => 'hourly', 'budget' => 65, 'status' => 'open'],
            ['title' => 'Content Writing for Blog', 'description' => 'Write 20 SEO-optimized blog posts.', 'budget_type' => 'fixed', 'budget' => 600, 'status' => 'open'],
            ['title' => 'Data Entry for CRM', 'description' => 'Enter 5000 customer records into CRM system.', 'budget_type' => 'fixed', 'budget' => 300, 'status' => 'closed'],
        ];
        
        $deadlines = [now()->addDays(15), now()->addDays(30), now()->addDays(45), now()->addDays(60)];
        
        foreach ($projects as $index => $data) {
            $client = $clients[$index % count($clients)];
            
            Project::create([
                'client_id' => $client->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'budget_type' => $data['budget_type'],
                'budget' => $data['budget'],
                'deadline' => $deadlines[array_rand($deadlines)],
                'status' => $data['status'],
            ]);
        }
    }
}