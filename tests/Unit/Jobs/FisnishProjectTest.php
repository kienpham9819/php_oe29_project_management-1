<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\FinishProject;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectSubmit;
use App\Models\User;
use App\Models\Project;
use App\Models\Group;

class FisnishProjectTest extends TestCase
{
    protected $project;
    protected $lecturer;
    protected $finishProject;

    public function setUp() : void
    {
        parent::setUp();
        $this->project = factory(Project::class)->make();
        $group = factory(Group::class)->make();
        $this->project->setRelation('group', $group);
        $this->lecturer = factory(User::class)->make();
        $this->finishProject = new FinishProject($this->project, $this->lecturer);
    }

    public function tearDown() : void
    {
        unset($this->project);
        unset($this->lecturer);
        unset($this->finishProject);
        parent::tearDown();
    }

    public function test_handle_success()
    {
        Mail::fake();
        $this->finishProject->handle();
        Mail::assertSent(ProjectSubmit::class, function ($mail) {
            return $mail->hasTo($this->lecturer);
        });
    }
}
