<?php

namespace App\Jobs;

use App\Models\Freelancer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class CalcFreelancerAvgRating implements ShouldQueue
{
    use Queueable;
    public $tries = 5;
    protected $freelancer;

    /**
     * Create a new job instance.
     */
    public function __construct(Freelancer $freelancer)
    {
        $this->freelancer = $freelancer;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $averageRating = $this->freelancer->reviews()->avg('rating') ?? 0;
        if ($this->freelancer->freelancerProfile) {
            $this->freelancer->freelancerProfile->update([
                'average_rating' => round($averageRating, 1),
                'rating_updated_at' => now(),
            ]);
        }
        Cache::flush();
    }
}
