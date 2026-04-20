<?php
namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $client = User::where('type', 'client')->first();
        
        $webTag = Tag::where('name', 'Web Development')->first();
        $larTag = Tag::where('name', 'Laravel')->first();
        $mobTag = Tag::where('name', 'Mobile Apps')->first();

        $p1 = Project::create([
            'client_id'   => $client->id,
            'title'       => 'Admin Dashboard Development',
            'description' => 'A completed project used to test the review system and attachments.',
            'budget_type' => 'fixed',
            'budget'      => 800.00,
            'deadline'    => now()->subDays(10),
            'status'      => 'closed',
        ]);
        
        if ($webTag && $larTag) {
            $p1->tags()->attach([$webTag->id, $larTag->id]);
        }

        $p2 = Project::create([
            'client_id'   => $client->id,
            'title'       => 'Flutter Mobile Application',
            'description' => 'Looking for a freelancer to implement mobile app interfaces.',
            'budget_type' => 'fixed',
            'budget'      => 1500.00,
            'deadline'    => now()->addMonths(1),
            'status'      => 'open',
        ]);

        if ($mobTag) {
            $p2->tags()->attach([$mobTag->id]);
        }

        // 3. Open Project (Restaurant Booking System)
        $p3 = Project::create([
            'client_id'   => $client->id,
            'title'       => 'Restaurant Booking System',
            'description' => 'Developing a Backend and API for managing reservations and customers.',
            'budget_type' => 'hourly',
            'budget'      => 30.00,
            'deadline'    => now()->addDays(15),
            'status'      => 'open',
        ]);

        if ($webTag && $larTag) {
            $p3->tags()->attach([$webTag->id, $larTag->id]);
        }
    }
}