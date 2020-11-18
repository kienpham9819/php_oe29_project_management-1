<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Faker\Generator as Faker;


class UserTest extends TestCase
{
    protected  $user;

    protected function setUp() : void
    {
        parent::setUp();
        $this->user = new User();
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->user = null;
    }

    public function test_user_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'name',
            'email',
            'password',
        ], $this->user->getFillable());
    }

    public function test_user_contains_valid_hidden_properties()
    {
        $this->assertEquals([
            'password',
            'remember_token',
            'github_token',
        ], $this->user->getHidden());
    }

    public function test_user_contains_valid_casts_properties()
    {
        $this->assertEquals([
            'id' => 'int',
            'email_verified_at' => 'datetime',
        ], $this->user->getCasts());
    }

    public function test_user_has_belongstomany_relationship_with_role()
    {
        $relation = $this->user->roles();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('role_id', $relation->getRelatedPivotKeyName ());
    }

    public function test_user_has_belongstomany_relationship_with_course()
    {
        $relation = $this->user->courses();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('course_id', $relation->getRelatedPivotKeyName ());
    }

    public function test_user_has_belongstomany_relationship_with_group()
    {
        $relation = $this->user->groups();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('group_id', $relation->getRelatedPivotKeyName ());
    }

    public function test_user_has_hasmany_relationship_with_course()
    {
        $relation = $this->user->teaches();
        $this->assertInstanceOf(HasMany::class, $relation);
    }

    public function test_user_has_hasmany_relationship_with_tasklist()
    {
        $relation = $this->user->taskLists();
        $this->assertInstanceOf(HasMany::class, $relation);
    }

    public function test_user_has_hasmany_relationship_with_comment()
    {
        $relation = $this->user->comments();
        $this->assertInstanceOf(HasMany::class, $relation);
    }

    public function test_user_has_hasmanythrough_relationship_with_task()
    {
        $relation = $this->user->tasks();
        $this->assertInstanceOf(HasManyThrough::class, $relation);
    }
}
