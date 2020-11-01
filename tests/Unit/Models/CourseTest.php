<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseTest extends TestCase
{
    protected  $course;

    protected function setUp() : void
    {
        parent::setUp();
        $this->course = new Course();
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->course = null;
    }

    public function test_course_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'name',
            'user_id',
        ], $this->course->getFillable());
    }

    public function test_course_has_hasmany_relationship_with_group()
    {
        $relation = $this->course->groups();
        $this->assertInstanceOf(HasMany::class, $relation);
    }

    public function test_course_has_belongsto_relationship_with_user()
    {
        $relation = $this->course->user();
        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    public function test_course_has_belongstomany_relationship_with_user()
    {
        $relation = $this->course->users();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('course_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('user_id', $relation->getRelatedPivotKeyName ());
    }
}
