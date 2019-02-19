<?php

namespace Tyler36\SocialAuth\Helpers;

class Providers
{
    /**
     * Services config must contain provider values for the following keys.
     *
     * @var array
     */
    protected static $requiredConfig = ['client_id', 'client_secret', 'redirect'];

    /**
     * Get an array of provider
     *
     * @return array
     */
    public static function getProviders()
    {
        return config('socialauth.providers', []);
    }

    /**
     * Get an array of enabled providers
     *
     * @return array
     */
    public static function getEnabled()
    {
        return array_filter(
            $providers = array_keys(self::getProviders(), true, true),
            function ($provider) {
                // if ('google' === $provider && ends_with(config('app.url'), '.local')) {
                //     return false;
                // }

                return self::isConfigured($provider);
            }
        );
    }

    /**
     * Check if provider is enabled
     *
     * @param null|mixed $provider
     *
     * @return bool
     */
    public static function isEnabled($provider = null)
    {
        return in_array($provider, self::getEnabled(), true);
    }

    /**
     * Access protected property for required config
     *
     * @return void
     */
    public static function getRequiredConfig()
    {
        return self::$requiredConfig;
    }

    /**
     * Check if provider has been configured
     *
     * @param null|mixed $provider
     *
     * @return bool
     */
    public static function isConfigured($provider = null)
    {
        return !array_diff(
            self::$requiredConfig,
            array_keys(config()->get("services.${provider}", []), true, false)
        );
    }
}
