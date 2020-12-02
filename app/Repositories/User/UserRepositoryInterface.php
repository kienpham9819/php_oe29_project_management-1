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

    public function hasRole($roleName);

    public function getUsersNoGroup($userIds, $groupIds);

    public function getUsersToAddGroup($userIds);

    public function addLeader($group, $leaderId);

    public function getLeader($groupId);

    public function changePassword($user, $password);

    public function hasPermissionTo($user, $permission);

    public function storeGithubToken($id, $token);

    public function getGithubRepositories($id, $userAgent);

    public function getNotifications($id);

    public function markAsRead($user, $notificationId);

    public function getLecturer($project);
}
