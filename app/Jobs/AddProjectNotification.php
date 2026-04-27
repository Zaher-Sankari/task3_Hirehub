<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class AddProjectNotification implements ShouldQueue
{
    use Queueable;

    protected $project;
    public $tries = 5;
    /**
     * Create a new job instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project; 
    }

    /**
     * Execute the job.
     * Send an email to the freelancers to inform then that new Project has beed added to the website:
     */
    public function handle(): void
    {
        User::where('type', 'freelancer')->chunk(50, function ($freelancers) {
            foreach ($freelancers as $freelancer) {
                Mail::raw("New project: {$this->project->title} - Budget: \${$this->project->budget}", function($message) use ($freelancer) {
                    $message->to($freelancer->email)
                            ->subject('New Project Available on HireHub');
                });
            }
            
        });
    }
}
