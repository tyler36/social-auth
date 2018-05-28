<?php

namespace Tyler36\SocialAuth\Helpers;

use Laravel\Socialite\Facades\Socialite;

class SocialUser
{
    public static function getUser()
    {
        return $this->getUserFromSocialite();
    }

    /**
     * Get a user from Socialite providers
     *
     * @param string $provider
     *
     * @return object
     */
    public function getUserFromSocialite($provider)
    {
        return Socialite::driver($provider)->user();
    }
}
