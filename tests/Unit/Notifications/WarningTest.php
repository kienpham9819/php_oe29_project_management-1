<?php

namespace Tests\Unit\Notifications;

use Tests\TestCase;
use App\Notifications\Warning;
use Illuminate\Support\Str;
use App\Models\User;

class WarningTest extends TestCase
{
    protected $notification, $data;

    public function setUp() : void
    {
        parent::setUp();
        $this->data = [
            'tasklistName' => Str::random(10),
            'url' => route('projects.task-lists.show', [1, 1]),
            'projectName' => Str::random(10),
        ];
        $this->notification = new Warning($this->data);
    }

    public function tearDown() : void
    {
        unset($this->notification);
        unset($this->data);
        parent::tearDown();
    }

    public function test_via_function()
    {
        $this->assertEquals(['database'], $this->notification->via(User::class));
    }

    public function test_representation_array()
    {
        $this->assertEquals($this->data, $this->notification->toArray(User::class));
    }
}
