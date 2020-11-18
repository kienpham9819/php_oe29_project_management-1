<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Models\Role;
use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return User::class;
    }

    public function getAll()
    {
        $users = User::paginate(config('paginate.record_number'));

        return $users;
    }

    public function getDeletedUser()
    {
        $users = User::onlyTrashed()->paginate(config('paginate.record_number'));

        return $users;
    }

    public function restoreUser($id)
    {
        return User::withTrashed()->where('id', $id)->restore();
    }

    public function forceDeleteUser($id)
    {
        return User::withTrashed()->where('id', $id)->forceDelete();
    }

    public function create($data = [])
    {
        $user = User::create($data);

        return $user->roles()->attach($data['roles']);
    }

    public function updateUser($user, $data, $roles)
    {
        $user->update($data);

        return $user->roles()->sync($roles);
    }

    public function import($request)
    {
        return Excel::import(new UsersImport, $request->file('file'));
    }

    public function getLecturers()
    {
        $lectures = Role::findOrFail(config('admin.lecturer'))->users()->get();

        return $lectures;
    }

    public function checkRoleForUser($user, $roleName)
    {
        $role = Role::where('slug', $roleName)->first();
        if ($role) {
            return $user->roles->contains($role);
        }

        return false;
    }

    public function getUsersToAddCourse($userIds)
    {
        $users = User::whereIn('id', $userIds)->get();

        return $users;
    }

    public function addUserToCourse($course, $userIds)
    {
        return $course->users()->attach($userIds);
    }

    public function deleteUserFromCourse($course, $userId)
    {
        $course->users()->detach($userId);
        $user = $this->find($userId);
        $group = $user->groups->where('course_id', $course->id)->first();
        if ($group) {
            return $group->users()->detach($userId);
        }

        return true;
    }

    public function getUsersNotInCourse($userIds)
    {
        $users = User::whereNotIn('id', $userIds)
            ->whereIn('id', function ($query) {
                $query->select('user_id')->from('role_user')
                    ->where('role_id', config('admin.student'))
                    ->orWhere('role_id', config('admin.leader'));
            })->get();

        return $users;
    }

    public function hasRole($roleName)
    {
        $user = auth()->user();
        $role = Role::where('slug', $roleName)->first();
        if ($role) {
            return $user->roles->contains($role);
        }

        return false;
    }

    public function getUsersNoGroup($userIds, $groupIds)
    {
        $users = User::whereIn('id', $userIds)
            ->whereNotIn('id', function ($query) use ($groupIds) {
                $query->select('user_id')->from('group_user')
                    ->whereIn('group_id', $groupIds);
            })->get();

        return $users;
    }

    public function getUsersToAddGroup($userIds)
    {
        $users = User::whereIn('id', $userIds)->get();

        return $users;
    }

    public function addLeader($group, $leaderId)
    {
        foreach ($group->users as $user) {
            $group->users()->updateExistingPivot($user->id, ['is_leader' => config('admin.isNotLeader')]);
        }
        $group->users()->updateExistingPivot($leaderId, ['is_leader' => config('admin.isLeader')]);
        Role::findOrFail(config('admin.leader'))->users()->syncWithoutDetaching($leaderId);

        return true;
    }

    public function getLeader($groupIds)
    {
        $leader = User::whereIn('id', function ($query) use ($groupIds) {
            $query->select('user_id')->from('group_user')->where('is_leader', config('admin.isLeader'))
                ->where('group_id', $groupIds);
        })->first();

        if ($leader) {
            return $leader;
        }

        return false;
    }
}
