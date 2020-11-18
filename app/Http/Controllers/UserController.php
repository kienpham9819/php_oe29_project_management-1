<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use App\Http\Requests\EditUserRequest;
use App\Imports\UsersImport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $userRepository;
    protected $roleRepository;
    protected $courseRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->courseRepository = $courseRepository;
    }

    public function index()
    {
        $users = $this->userRepository->getAll();
        $roles = $this->roleRepository->getAll();
        $newCourses = $this->courseRepository->getLatestCourses();

        return view('users.admin.list', compact(['users', 'roles', 'newCourses']));
    }

    public function deleted()
    {
        $users = $this->userRepository->getDeletedUser();
        $roles = $this->roleRepository->getAll();
        $newCourses = $this->courseRepository->getLatestCourses();

        return view('users.admin.restore', compact(['users', 'roles', 'newCourses']));
    }

    public function restore($id)
    {
        $this->userRepository->restoreUser($id);

        return redirect()->route('users.deleted');
    }

    public function forceDelete($id)
    {
        $this->userRepository->forceDeleteUser($id);

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
        $this->userRepository->create($data);

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_add'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        $roles = $this->roleRepository->getAll();
        $newCourses = $this->courseRepository->getLatestCourses();

        return view('users.admin.edit', compact(['user', 'roles', 'newCourses']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditUserRequest $request, $id)
    {
        $user = $this->userRepository->find($id);
        $dataUpdate = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        if (!empty($request->password)) {
            $dataUpdate['password'] = Hash::make($request->password);
        }
        $this->userRepository->updateUser($user, $dataUpdate, $request->roles);

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->userRepository->delete($id);

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_delete'));
    }

    public function import(Request $request)
    {
        $this->userRepository->import($request);

        return redirect()->route('users.index')
            ->with('message', trans('user.noti_import'));
    }
}
