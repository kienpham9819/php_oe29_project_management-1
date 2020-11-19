<?php

namespace App\Repositories\Group;

use App\Repositories\RepositoryInterface;

interface GroupRepositoryInterface extends RepositoryInterface
{
    public function addUsersToGroup($group, $userIds);

    public function deleteUser($group, $user);

    public function getGroupHasProject($user);

    public function getGroupForStudentInCourse($user, $courseId);
}
