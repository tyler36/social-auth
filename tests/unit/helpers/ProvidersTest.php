<?php

namespace Tests\Unit;

use Tyler36\SocialAuth\Helpers\Providers;
use Tests\TestCase;

/**
 * Class ProvidersTest
 *
 * @test
 * @group unit
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ProvidersTest extends TestCase
{
    /**
     * Setup social provider configurations
     *
     * @param [type] $name
     *
     * @return void
     */
    public static function setupValidProvider($name = null)
    {
        $name = $name ?: 'valid_provider';

        config()->set("socialauth.providers.${name}", true);

        foreach (Providers::getRequiredConfig() as $key => $value) {
            config()->set("services.${name}.${value}", true);
        }

        return $name;
    }

    /**
     * @test
     */
    public function it_can_get_an_array_of_providers_from_config()
    {
        $providers =  [
            'github'  => true,
            'twitter' => false,
        ];

        // SETUP:       Config
        config()->set('socialauth.providers', $providers);

        // ASSERT:      Array contains providers
        $this->assertArraySubset($providers, Providers::getProviders());
    }

    /**
     * @test
     */
    public function it_can_get_an_array_of_enabled_providers_from_config()
    {
        // SETUP:       Config
        config()->set('services.github', [
            'client_id'     => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'redirect'      => 'test_redirect'
        ]);
        config()->set('services.twitter', [
            'client_id'     => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'redirect'      => 'test_redirect'
        ]);

        config()->set('socialauth.providers', [
            'github'  => true,
            'twitter' => false,
        ]);

        // ASSERT:      Array contains providers
        $this->assertArraySubset(['github'], Providers::getEnabled());
    }

    /**
     * @test
     */
    public function it_can_check_if_provider_has_required_configuration_set()
    {
        // SETUP:       Config
        $provider = 'valid_provider';
        config()->set('socialauth.providers', [
            $provider => true,
        ]);

        // ASSERT:      Provider is NOT configured
        $this->assertFalse(Providers::isConfigured($provider));

        // SETUP:      Provider is configured
        config()->set("services.${provider}", [
            'client_id'     => true,
            'client_secret' => true,
            'redirect'      => true,
        ]);

        // ASSERT:      Provider is configured
        $this->assertTrue(Providers::isConfigured($provider));
    }

    /**
     * @test
     * @group google
     * @group error
     */
    public function google_is_disabled_if_environment_is_on_non_public_tld()
    {
        $this->markTestSkipped('// Disabled ');
        // Google throws an error when using a non-public TLD
        // SETUP:       Google is a valid provider
        $provider = $provider = self::setupValidProvider('google');

        // ASSERT:          Google is OK with valid domain
        config()->set('app.url', 'http://valid.dev');
        $this->assertTrue(Providers::isEnabled($provider));

        // ASSERT:          Google is invalid with non-public TLD
        config()->set('app.url', 'http://invalid.local');
        $this->assertFalse(Providers::isEnabled($provider));
    }
}
