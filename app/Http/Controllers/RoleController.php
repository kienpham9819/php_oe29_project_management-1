<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Course;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Http\Requests\RoleRequest;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Permission\PermissionRepositoryInterface;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $roleRepository;
    protected $courseRepository;
    protected $permissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        CourseRepositoryInterface $courseRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->middleware('auth');
        $this->roleRepository = $roleRepository;
        $this->courseRepository = $courseRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function index()
    {
        $roles = $this->roleRepository->getAll();
        $newCourses = $this->courseRepository->getLatestCourses();

        return view('users.admin.role_list', compact(['roles', 'newCourses']));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $role = $this->roleRepository->find($id);
        $permissions = $this->permissionRepository->getAll();
        $newCourses = $this->courseRepository->getLatestCourses();

        return view('users.admin.role_edit', compact(['role', 'permissions', 'newCourses']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, RoleRequest $request)
    {
        $this->roleRepository->update($id, $request->permission);

        return redirect()->route('roles.index')
            ->with('message', trans('role.noti_edit'));
    }
}
