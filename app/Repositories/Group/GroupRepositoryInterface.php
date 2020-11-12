<?php

namespace App\Repositories\Group;

interface GroupRepositoryInterface
{
    public function addUsersToGroup($group, $userIds);

    public function deleteUser($group, $user);
}
