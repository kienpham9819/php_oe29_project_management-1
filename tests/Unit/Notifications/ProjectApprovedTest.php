<?php

namespace Tests\Unit\Notifications;

use Tests\TestCase;
use App\Notifications\ProjectApproved;
use Illuminate\Support\Str;
use App\Models\User;

class ProjectApprovedTest extends TestCase
{
    protected $notification, $projectName, $url;

    public function setUp() : void
    {
        parent::setUp();
        $this->url = route('projects.task-lists.show', [1, 1]);
        $this->projectName = Str::random(10);
        $this->notification = new ProjectApproved($this->projectName, $this->url);
    }

    public function tearDown() : void
    {
        unset($this->notification);
        unset($this->projectName);
        unset($this->url);
        parent::tearDown();
    }

    public function test_via_function()
    {
        $this->assertEquals(['database'], $this->notification->via(User::class));
    }

    public function test_representation_array()
    {
        $this->assertEquals([
            'projectName' => $this->projectName,
            'url' => $this->url,
        ], $this->notification->toDatabase(User::class));
    }
}
