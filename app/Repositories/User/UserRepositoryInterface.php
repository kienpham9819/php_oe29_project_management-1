<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function getDeletedUser();

    public function restoreUser($id);

    public function forceDeleteUser($id);

    public function updateUser($user, $data, $roles);

    public function import($request);

    public function hasRole($roleName);

    public function getUsersNoGroup($userIds, $groupIds);

    public function getUsersToAddGroup($userIds);

    public function checkRoleForUser($user, $roleName);

    public function addLeader($group, $leaderId);

    public function getLeader($groupIds);
}
