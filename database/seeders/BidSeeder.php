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
        // Find an open project to bid on
        $openProject = Project::where('status', 'open')->first();
        
        // Get two different freelancers
        $freelancer1 = User::where('type', 'freelancer')->first();
        $freelancer2 = User::where('type', 'freelancer')->skip(1)->first(); 

        // Clear existing bids for this project to avoid conflicts during re-seeding
        Bid::where('project_id', $openProject->id)->delete();

        Bid::insert([
            [
                'project_id'      => $openProject->id,
                'freelancer_id'   => $freelancer1->id,
                'amount'          => 1400.00,
                'proposal' => 'I have 3 years of experience in Flutter and will meet the deadline.',
                'delivery_days'   => 15,
                'status'          => 'accepted',
            ],
            [
                'project_id'      => $openProject->id,
                'freelancer_id'   => $freelancer2->id,
                'amount'          => 1600.00,
                'proposal' => 'I will provide professional interfaces with continuous support.',
                'delivery_days'   => 20,
                'status'          => 'pending',
            ],
        ]);
    }
}