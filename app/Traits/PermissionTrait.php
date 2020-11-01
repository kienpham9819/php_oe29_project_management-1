<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait PermissionTrait
{
    public function hasRole($roleName)
    {
        $role = Role::where('slug', $roleName)->first();
        if ($role) {
            return $this->roles->contains($role);
        }

        return false;
    }

    public function hasPermissionTo(Permission $permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }

        return false;
    }
}
