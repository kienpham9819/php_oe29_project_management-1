<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Http\Requests\UserRequest;
use App\Http\Requests\EditUserRequest;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
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
        $users = User::orderBy('updated_at', 'desc')->paginate(config('paginate.record_number'));
        $roles = Role::all();
        $newCourses = getLatestCourses();

        return view('users.admin.list', compact(['users', 'roles', 'newCourses']));
    }

    public function deleted()
    {
        $users = User::onlyTrashed()->paginate(config('paginate.record_number'));
        $roles = Role::all();
        $newCourses = getLatestCourses();

        return view('users.admin.restore', compact(['users', 'roles', 'newCourses']));
    }

    public function restore($id)
    {
        User::withTrashed()->where('id', $id)->restore();

        return redirect()->route('users.deleted');
    }

    public function forceDelete($id)
    {
        User::withTrashed()->where('id', $id)->forceDelete();

        return redirect()->route('users.deleted')
            ->with('message', trans('user.noti_delete'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request['password']);
        $user = User::create($data);
        $user->roles()->attach($data['roles']);

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_add'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $newCourses = getLatestCourses();

        return view('users.admin.edit', compact(['user', 'roles', 'newCourses']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditUserRequest $request, User $user)
    {
        $dataUpdate = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        if (!empty($request->password)) {
            $dataUpdate['password'] = Hash::make($request->password);
        }
        $user->roles()->sync($request->roles);

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_delete'));
    }

    public function import(Request $request)
    {
        Excel::import(new UsersImport, $request->file('file'));

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_import'));
    }
}
