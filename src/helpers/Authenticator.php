<?php

namespace Tyler36\SocialAuth\Helpers;

use App\User;
use Tyler36\SocialAuth\SocialAuthServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class Authenticator
{
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
     * @return Redirect
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
     * @return Redirect
     */
    public function redirectToProvider()
    {
        return Socialite::driver($this->provider)
            ->redirect();
    }

    /**
     * Handle callback response from Provider
     *
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
     */
    public function updateUser($user, $socialUser)
    {
        $user->fill([
            'name'  => $socialUser->nickname,
            'email' => ('' !== $socialUser->email) ? $socialUser->email : null,
        ])->save();

        return $this->validateUser($user);
    }

    /**
     * Check if User fails 'unique' name validation
     *
     * @param mixed $user
     *
     * @return void
     */
    public function userHasDuplicateName($user)
    {
        return in_array(trans('validation.unique', ['attribute' => 'name']), $user->getErrors()->all(), true);
    }

    /**
     * Validate User object
     *
     * @param [type] $user
     *
     * @return void
     */
    public function validateUser($user)
    {
        if ($user->isValid()) {
            $user->save();

            return $user;
        }

        $messageBag = clone $user->getErrors();

        if ($this->userHasDuplicateName($user)) {
            $user->name = $user->auth_provider.'-'.str_random(5);
        }

        $user->forceSave();
        $user->setErrors($messageBag);

        return $user;
    }
}
