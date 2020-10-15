<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Course;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Role::all();
        $newCourses = getLatestCourses();

        return view('users.admin.role_list', compact(['roles', 'newCourses']));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $newCourses = getLatestCourses();

        return view('users.admin.role_edit', compact(['role', 'permissions', 'newCourses']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role->permissions()->sync($request->permission);

        return redirect()->route('roles.index')
            ->with('message', trans('role.noti_edit'));
    }
}
