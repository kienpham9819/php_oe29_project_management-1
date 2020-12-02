<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ProjectSubmit;
use Mail;

class FinishProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $project;
    protected $lecturer;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($project, $lecturer)
    {
        $this->project = $project;
        $this->lecturer = $lecturer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->lecturer)->send(new ProjectSubmit($this->project, $this->lecturer));
    }
}
