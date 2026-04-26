<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    public function run(): void
    {
        $freelancers = User::where('type', 'freelancer')->get();
        $projects = Project::all();
        
        foreach ($projects as $project) {
            $numBids = rand(0, 5);
            $shuffledFreelancers = $freelancers->shuffle();
            
            for ($i = 0; $i < $numBids && $i < $shuffledFreelancers->count(); $i++) {
                $freelancer = $shuffledFreelancers[$i];
                
                $exists = Bid::where('project_id', $project->id)
                    ->where('freelancer_id', $freelancer->id)
                    ->exists();
                
                if (!$exists) {
                    $amount = $project->budget_type === 'fixed'
                        ? $project->budget - rand(100, min($project->budget - 50, 500))
                        : $project->budget + rand(-10, 20);
                    
                    $amount = max($amount, $project->budget_type === 'fixed' ? 10 : 5);
                    
                    $status = 'pending';
                    if ($project->status === 'closed') {
                        $status = 'rejected';
                    } elseif ($project->status === 'in_progress' && $i === 0) {
                        $status = 'accepted';
                    }
                    
                    Bid::create([
                        'project_id' => $project->id,
                        'freelancer_id' => $freelancer->id,
                        'amount' => round($amount, 2),
                        'delivery_days' => rand(5, 45),
                        'proposal' => "I'm interested in this project. I have experience with similar projects.",
                        'status' => $status,
                    ]);
                }
            }
        }
    }
}