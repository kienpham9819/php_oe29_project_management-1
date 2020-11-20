<?php

namespace App\Traits;

use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App;

trait PermissionTrait
{
    protected static $userRepository;

    protected static function bootPermissionTrait()
    {
        static::$userRepository = App::make(UserRepositoryInterface::class);
    }

    public function hasRole($roleName)
    {
        return static::$userRepository->checkRoleForUser($this, $roleName);
    }

    public function hasPermissionTo($permission)
    {
        return static::$userRepository->hasPermissionTo($this, $permission);
    }
}
