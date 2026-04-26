<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $acceptedBids = Bid::where('status', 'accepted')->get();
        
        $comments = [
            'Excellent work! Delivered on time.',
            'Great communication and professional approach.',
            'Good quality work, would recommend.',
            'Amazing freelancer! Very skilled.',
            'Satisfied with the work.',
            'Outstanding! One of the best.',
        ];
        
        foreach ($acceptedBids as $bid) {
            $client = User::find($bid->project->client_id);
            $freelancer = User::find($bid->freelancer_id);
            
            if ($client && $freelancer) {
                // Review for freelancer
                Review::create([
                    'reviewer_id' => $client->id,
                    'project_id' => $bid->project_id,
                    'reviewable_type' => 'App\\Models\\Freelancer',
                    'reviewable_id' => $freelancer->id,
                    'rating' => rand(4, 5),
                    'comment' => $comments[array_rand($comments)],
                ]);
                
                // Review for project
                Review::create([
                    'reviewer_id' => $freelancer->id,
                    'project_id' => $bid->project_id,
                    'reviewable_type' => 'App\\Models\\Project',
                    'reviewable_id' => $bid->project_id,
                    'rating' => rand(3, 5),
                    'comment' => 'Clear requirements and good client communication.',
                ]);
            }
        }
    }
}