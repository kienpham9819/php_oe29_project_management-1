<?php

namespace App\Repositories\Group;

use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Group;

class GroupRepository extends BaseRepository implements GroupRepositoryInterface
{
    public function getModel()
    {
        return Group::class;
    }

    public function addUsersToGroup($group, $userIds)
    {
        return $group->users()->attach($userIds);
    }

    public function deleteUser($group, $user)
    {
        return $group->users()->detach($user);
    }

    public function delete($groupId)
    {
        $group = Group::findOrFail($groupId);
        if ($group) {
            $users = $group->users;
            $project = $group->project;
            if ($project) {
                $project->delete();
            }
            if ($users) {
                $group->users()->detach($users);
            }
            $group->delete();

            return true;
        }

        return false;
    }

    public function getGroupHasProject($user)
    {
        $groups = $user->groups()
            ->has('project')
            ->pluck('groups.id');

        return $groups;
    }

    public function getGroupForStudentInCourse($user, $courseId)
    {
        $group = $user->groups()->where('course_id', $courseId)->first();

        return $group;
    }
}
