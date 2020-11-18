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
        if (config()->has('services.' . $driver)) {
            return Socialite::driver($driver)
                ->scopes(['read:user', 'repo'])
                ->redirect();
        } else {
            abort(404);
        }
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */

    public function handleProviderCallback($driver)
    {
        try {
            $user = Socialite::driver($driver)->user();
        } catch (Exception $e) {
            abort(403);
        }

        // store user's github token
        $this->userRepository->storeGithubToken(auth()->user()->id, $user->token);

        return redirect()->route('home');
    }
}
