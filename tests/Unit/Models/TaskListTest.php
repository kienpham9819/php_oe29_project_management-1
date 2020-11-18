<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class TaskListTest extends TestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TaskList();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->model);
    }

    public function test_role_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'name',
            'description',
            'due_date',
            'user_id',
            'project_id',
        ], $this->model->getFillable());
    }

    public function test_contains_valid_dates_properties()
    {
        $this->assertEquals([
            'due_date',
            'start_date',
            'deleted_at',
            'created_at',
            'updated_at',
        ], $this->model->getDates());
    }

    public function test_project_relation()
    {
        $relation = $this->model->project();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('project_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    public function test_user_relation()
    {
        $relation = $this->model->user();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    public function test_comments_relation()
    {
        $relation = $this->model->comments();
        $this->assertInstanceOf(HasManyThrough::class, $relation);
        $this->assertEquals('task_list_id', $relation->getFirstKeyName());
        $this->assertEquals('task_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getLocalKeyName());
        $this->assertEquals('id', $relation->getSecondLocalKeyName());
    }

    public function test_tasks_relation()
    {
        $relation = $this->model->tasks();
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('task_list_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getLocalKeyName());
    }
}
