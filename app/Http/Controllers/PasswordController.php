<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            $request->user()->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('home');
        } else {
            return redirect()->back()->withErrors(['old_password' => trans('auth.failed')]);
        }
    }
}
