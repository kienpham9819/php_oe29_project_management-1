<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentTest extends TestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Comment();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->model);
    }

    public function test_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'user_id',
            'content',
            'task_id',
        ], $this->model->getFillable());
    }

    public function test_task_relation()
    {
        $relation = $this->model->task();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('task_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }

    public function test_user_relation()
    {
        $relation = $this->model->user();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }
}
