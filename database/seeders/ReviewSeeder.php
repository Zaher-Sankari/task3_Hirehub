<?php
namespace Database\Seeders;

use App\Models\Review;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $closedProject = Project::where('status', 'closed')->first();
        $client        = User::where('type', 'client')->first();
        $freelancer    = User::where('type', 'freelancer')->first();

        Review::insert([
            [
                'client_id'       => $client->id,
                'project_id'      => $closedProject->id,
                'rating'          => 5,
                'comment'         => 'Professional developer, highly recommended.',
                'reviewable_type' => User::class,
                'reviewable_id'   => $freelancer->id,
            ],
            [
                'client_id'       => $client->id,
                'project_id'      => $closedProject->id,
                'rating'          => 4,
                'comment'         => 'Clean code and excellent speed. The project runs efficiently.',
                'reviewable_type' => Project::class,
                'reviewable_id'   => $closedProject->id,
            ],
        ]);
    }
}