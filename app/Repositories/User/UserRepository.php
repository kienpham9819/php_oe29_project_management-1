<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Models\Role;
use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;

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

    public function storeGithubToken($id, $token)
    {
        $user = $this->find($id);
        if ($user) {
            $user->github_token = $token;
            $user->save();

            return true;
        }

        return false;
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

    public function getLeader($groupId)
    {
        $leader = User::whereIn('id', function ($query) use ($groupId) {
            $query->select('user_id')->from('group_user')->where('is_leader', config('admin.isLeader'))
                ->where('group_id', $groupId);
        })->first();

        if ($leader) {
            return $leader;
        }

        return false;
    }

    public function changePassword($user, $password)
    {
        return $user->update([
            'password' => Hash::make($password),
        ]);
    }

    public function hasPermissionTo($user, $permission)
    {
        $roles = $permission->roles;
        foreach ($roles as $role) {
            if ($user->roles->contains($role)) {
                return true;
            }
        }

        return false;
    }

    public function getGithubRepositories($id, $userAgent)
    {
        $user = $this->find($id);
        if ($user) {
            if ($user->github_token) {
                $curl_token = 'Authorization: token ' . $user->github_token;
                $curl_handle = curl_init(config('github.curl_repo_url'));
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
                    'User-Agent:' . $userAgent,
                    $curl_token,
                ]);
                $repositories = curl_exec($curl_handle);
                curl_close($curl_handle);
                $repositories = json_decode($repositories);

                return $repositories;
            }

            return [];
        }

        return false;
    }

    public function getNotifications($id)
    {
        $user = $this->find($id);
        if ($user) {
            $notifications = [];
            foreach ($user->unreadNotifications->sortBy('created_at') as $notification) {
                $message = trans(config('notification.' . $notification->type), $notification->data);
                $notifications[] = (object) [
                    'message' => $message,
                    'url' => $notification->data['url'],
                    'id' => $notification->id,
                    'created_at' => $notification->created_at,
                ];
            }

            return $notifications;
        } else {
            return [];
        }
    }

    public function markAsRead($user, $notificationId)
    {
        $user = $this->find($user);
        if ($user) {
            $user->notifications()->where('id', $notificationId)->update(['read_at' => now()]);

            return true;
        }

        return false;
    }

    public function getLecturer($project)
    {
        $project->load('group.course.user');
        $lecturer = $project->group->course->user;

        return $lecturer;
    }
}
