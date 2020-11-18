<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PasswordRequest;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class PasswordController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
    }

    public function editPassword(Request $request)
    {
        $user = $request->user();

        return view('users.change_password', compact(['user']));
    }

    public function updatePassword(PasswordRequest $request)
    {
        if (auth()->attempt([
            'email' => auth()->user()->email,
            'password' => $request->old_password,
        ])) {
            $this->userRepository->changePassword($request->user(), $request->password);
            if ($this->userRepository->hasRole('admin')) {
                return redirect()->route('users.index');
            } elseif ($this->userRepository->hasRole('lecturer')) {
                return redirect()->route('lecturers.courseList');
            }

            return redirect()->route('students.courseList');
        } else {
            return redirect()->back()->withErrors(['old_password' => trans('auth.failed')]);
        }
    }
}
