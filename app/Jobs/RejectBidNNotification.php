<?php

namespace App\Jobs;

use App\Models\Bid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class RejectBidNNotification implements ShouldQueue
{
    use Queueable;
    public $tries = 5;
    protected $bid;
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
    }

    /**
     * Execute the job.
     */   public function handle()
    {
        Mail::raw("Your bid of \${$this->bid->amount} for project '{$this->bid->project->title}' got rejected.", function($message) {
            $message->to($this->bid->freelancer->email)->subject('Bid Not Accepted - HireHub');
        });
    }
}
