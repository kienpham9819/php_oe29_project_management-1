<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_permission_has_belongstomany_relationship_with_role()
    {
        $permission = new Permission();
        $relation = $permission->roles();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('permission_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('role_id', $relation->getRelatedPivotKeyName ());
    }
}
