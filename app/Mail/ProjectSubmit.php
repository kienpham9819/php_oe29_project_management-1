<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use App\Models\User;

class ProjectSubmit extends Mailable
{
    use Queueable, SerializesModels;

    protected $project, $lecturer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Project $project, User $lecturer)
    {
        $this->project = $project;
        $this->lecturer = $lecturer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.submit')
            ->with([
                'project' => $this->project,
                'lecturer' => $this->lecturer,
            ]);
    }
}
