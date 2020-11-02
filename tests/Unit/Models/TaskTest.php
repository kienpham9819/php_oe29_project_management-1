<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskTest extends TestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Task();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->model);
    }

    public function test_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'name',
            'task_list_id',
            'is_completed',
        ], $this->model->getFillable());
    }

    public function test_comments_relation()
    {
        $relation = $this->model->comments();
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('task_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getLocalKeyName());
    }

    public function test_attachments_relation()
    {
        $relation = $this->model->attachments();
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('task_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getLocalKeyName());
    }

    public function test_task_list_relation()
    {
        $relation = $this->model->taskList();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('task_list_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }
}
