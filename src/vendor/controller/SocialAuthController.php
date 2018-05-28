<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Tyler36\SocialAuth\Helpers\Authenticator;

class SocialAuthController extends Controller
{
    /**
     * AuthSocialiteController constructor
     */
    public function __construct(Authenticator $authenticator)
    {
        $this->middleware(['web', 'guest']);

        $this->authenticator = $authenticator;
    }

    /**
     * Redirect the user to provider
     */
    public function login()
    {
        return $this->authenticator->logInToProvider();
    }


    /**
     * Handle callback response from Socialite provider
     */
    public function callback()
    {
        $user = $this->authenticator->respondToCallback();

        if ($user->isInValid() || $user->getErrors()->any()) {
            return redirect()->route('user.edit', $user)
                ->withErrors($user->getErrors());
        }

        return redirect()->route('home');
    }
}
