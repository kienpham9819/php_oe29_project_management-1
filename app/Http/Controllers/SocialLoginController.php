<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Repositories\User\UserRepositoryInterface;

class SocialLoginController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */

    public function redirectToProvider($driver)
    {
        if (!config()->has('services.' . $driver)) {
            abort(404);
        }

        return Socialite::driver($driver)
            ->scopes(['read:user', 'repo'])
            ->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */

    public function handleProviderCallback($driver)
    {
        if (config()->has('services.' . $driver)) {
            $user = Socialite::driver($driver)->user();
        } else {
            abort(404);
        }

        // store user's github token
        $this->userRepository->storeGithubToken(auth()->user()->id, $user->token);

        return redirect()->route('home');
    }
}
