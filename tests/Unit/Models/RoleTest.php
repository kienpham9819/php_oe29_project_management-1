<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleTest extends TestCase
{
    protected  $role;

    protected function setUp() : void
    {
        parent::setUp();
        $this->role = new Role();
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->role = null;
    }

    public function test_role_contains_valid_fillable_properties()
    {
        $this->assertEquals([
            'name',
            'slug',
        ], $this->role->getFillable());
    }

    public function test_role_has_belongstomany_relationship_with_user()
    {
        $relation = $this->role->users();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('role_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('user_id', $relation->getRelatedPivotKeyName());
    }

    public function test_role_has_belongstomany_relationship_with_permission()
    {
        $relation = $this->role->permissions();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('role_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('permission_id', $relation->getRelatedPivotKeyName());
    }
}
