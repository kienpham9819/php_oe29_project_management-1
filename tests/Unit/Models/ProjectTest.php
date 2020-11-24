<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ProjectTest extends TestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Project();
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
            'description',
            'group_id',
            'review',
            'grade',
            'is_completed',
            'git_repository',
        ], $this->model->getFillable());
    }

    public function test_group_relation()
    {
        $relation = $this->model->group();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('group_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    public function test_task_lists_relation()
    {
        $relation = $this->model->taskLists();
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('project_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getLocalKeyName());
    }

    public function test_tasks_relation()
    {
        $relation = $this->model->tasks();
        $this->assertInstanceOf(HasManyThrough::class, $relation);
        $this->assertEquals('project_id', $relation->getFirstKeyName());
        $this->assertEquals('task_list_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getLocalKeyName());
        $this->assertEquals('id', $relation->getSecondLocalKeyName());
    }
}
