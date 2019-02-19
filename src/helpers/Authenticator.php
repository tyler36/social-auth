<?php

namespace Tyler36\SocialAuth\Helpers;

use App\User;
use Laravel\Socialite\Facades\Socialite;
use Tyler36\SocialAuth\SocialAuthServiceProvider;
use Illuminate\Support\Facades\Validator;

class Authenticator
{
    /**
     * Name of authenticating social servive
     *
     * @var string
     */
    public $provider;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // SETUP:       Get Socialite provider
        $this->provider = ProviderRoute::get();
    }

    /**
     * Make request to Provider
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logInToProvider()
    {
        return $this
            ->validateProvider()
            ->redirectToProvider();
    }

    /**
     * Redirect to provider.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider()
    {
        return Socialite::driver($this->provider)
            ->with(['locale' => app()->getLocale()])
            ->redirect();
    }

    /**
     * Handle callback response from provider
     *
     * @return User
     */
    public function respondToCallback()
    {
        $this->validateProvider();

        // Get User
        $user = $this->getUser();

        //  Login User
        auth()->login($user);

        return $user;
    }

    /**
     * Throw error if Provider is invalid
     *
     * @return Authenticator
     */
    public function validateProvider()
    {
        abort_unless(
            Providers::isEnabled($this->provider),
            401,
            trans(SocialAuthServiceProvider::$namespace.'::message.unavailable', ['provider' => $this->provider])
        );

        return $this;
    }

    /**
     * Retrieve User from database
     *
     * @return User
     */
    public function getUser()
    {
        $socialUser = $this->getSocialUserObject();

        // Check for existing member
        $user       = User::firstOrNew([
            'auth_provider'    => $this->provider,
            'auth_provider_id' => $socialUser->id,
        ]);

        if (!$user->exists) {
            $user = $this->updateUser($user, $socialUser);
        }

        return $user;
    }

    /**
     * Get Socialite object
     *
     * @return \Laravel\Socialite\One\User|\Laravel\Socialite\Two\User
     */
    public function getSocialUserObject()
    {
        return Socialite::driver($this->provider)->user();
    }

    /**
     * Update User model with Social data
     *
     * @param object $user       User model
     * @param object $socialUser Socialite user object
     *
     * @return bool
     */
    public function updateUser($user, $socialUser)
    {
        $user->fill([
            'name'  => $socialUser->nickname ?? $socialUser->name,
            'email' => ('' !== $socialUser->email) ? $socialUser->email : null,
        ])->save();

        return $user;
    }
}
