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
}
