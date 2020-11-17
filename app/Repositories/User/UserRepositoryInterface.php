<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function getDeletedUser();

    public function restoreUser($id);

    public function forceDeleteUser($id);

    public function updateUser($user, $data, $roles);

    public function import($request);

    public function getLecturers();

    public function checkRoleForUser($user, $roleName);

    public function getUsersToAddCourse($userIds);

    public function addUserToCourse($courseId, $userIds);

    public function deleteUserFromCourse($course, $userId);

    public function getUsersNotInCourse($userIds);
}
