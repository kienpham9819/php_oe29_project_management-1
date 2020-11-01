<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Group;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GroupTest extends TestCase
{
    protected  $group;

    protected function setUp() : void
    {
        parent::setUp();
        $this->group = new Group();
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->group = null;
    }

    public function test_group_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'name',
            'course_id',
        ], $this->group->getFillable());
    }

    public function test_group_has_belongstomany_relationship_with_user()
    {
        $relation = $this->group->users();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('group_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('user_id', $relation->getRelatedPivotKeyName ());
    }

    public function test_group_has_belongsto_relationship_with_course()
    {
        $relation = $this->group->course();
        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    public function test_group_has_hasone_relationship_with_project()
    {
        $relation = $this->group->project();
        $this->assertInstanceOf(HasOne::class, $relation);
    }
}
