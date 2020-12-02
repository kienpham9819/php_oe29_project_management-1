<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectSubmit;
use Illuminate\Mail\Markdown;
use ReflectionClass;
use App\Models\Project;
use App\Models\User;
use App\Models\Group;

class ProjectSubmitTest extends TestCase
{
    protected $project, $lecturer, $notiFinishProject;

    public function setUp() : void
    {
        parent::setUp();
        $this->project = factory(Project::class)->make();
        $this->project->id = 1;
        $group = factory(Group::class)->make();
        $this->project->setRelation('group', $group);
        $this->lecturer = factory(User::class)->make();
        $this->notiFinishProject = new ProjectSubmit($this->project, $this->lecturer);
    }

    public function tearDown() : void
    {
        unset($this->project);
        unset($this->lecturer);
        unset($this->notiFinishProject);
        parent::tearDown();
    }

    public function test_build_function()
    {
        Mail::fake();
        Mail::send($this->notiFinishProject);
        Mail::assertSent(ProjectSubmit::class, function () {
            $this->notiFinishProject->build();
            $reflectionClass = new ReflectionClass($this->notiFinishProject);
            $reflectionProperty = $reflectionClass->getProperty('markdown');
            $reflectionProperty->setAccessible(true);
            $view = $reflectionProperty->getValue($this->notiFinishProject);
            $markdown = $this->app->make(Markdown::class);
            $body = $markdown->renderText($view, $this->notiFinishProject->buildViewData())->toHtml();
            $this->assertEquals($this->project, $this->notiFinishProject->buildViewData()['project']);
            $this->assertEquals($this->lecturer, $this->notiFinishProject->buildViewData()['lecturer']);

            return true;
        });
    }
}
