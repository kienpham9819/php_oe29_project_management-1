<?php

namespace App\Repositories\Role;

use App\Repositories\RepositoryInterface;

interface RoleRepositoryInterface extends RepositoryInterface
{
   public function update($id, $permissionIds = []);
}
