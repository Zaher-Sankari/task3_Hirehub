<?php

namespace App\Jobs;

use App\Models\Bid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class AcceptBidNNotification implements ShouldQueue
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
     */
    public function handle(): void
    {
    //freelancer notification:
        Mail::raw("your bid of {$this->bid->amount} for the project {$this->bid->project->title}
            was accepted!",function($message){
                $message->to($this->bid->freelancer->email)->subject("You bid has beed accepted for {$this->bid->project->title}");
            }
    );

            
    // client notification:
            Mail::raw("You accepted the bid from {$this->bid->freelancer->first_name} 
            {$this->bid->freelancer->last_name} for \${$this->bid->amount}", function($message) {
                $message->to($this->bid->project->client->email)->subject('You Accepted a Bid - HireHub');
            });
        }
    }
