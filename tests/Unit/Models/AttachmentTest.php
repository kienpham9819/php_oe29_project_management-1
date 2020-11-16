<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttachmentTest extends TestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Attachment();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->model);
    }

    public function test_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'task_id',
            'url',
            'name',
        ], $this->model->getFillable());
    }

    public function test_task_relation()
    {
        $relation = $this->model->task();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('task_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
    }
}
