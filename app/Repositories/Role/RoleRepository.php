<?php

namespace App\Repositories\Role;

use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function getModel()
    {
        return Role::class;
    }

    public function update($id, $permissionIds = [])
    {
        $role = $this->find($id);
        if ($role) {
            $role->permissions()->sync($permissionIds);

            return $role;
        }

        return false;
    }
}
