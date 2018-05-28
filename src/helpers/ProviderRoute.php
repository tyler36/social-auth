<?php

namespace Tyler36\SocialAuth\Helpers;

use Route;

class ProviderRoute
{
    /**
     * Get the Socialite provider from route.
     *
     * @return mixed
     */
    public static function get()
    {
        if (null === Route::current()) {
            return false;
        }

        return Route::current()->parameters()['provider'];
    }
}
