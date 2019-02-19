<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tyler36\SocialAuth\Helpers\Authenticator;
use Tests\TestCase;

/**
 * Class AuthenticatorTest
 *
 * @test
 * @group unit
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class AuthenticatorTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->authenticator = new Authenticator();
    }

    /**
     * @test
     * @group exception
     */
    public function it_throws_an_exception_if_a_provider_is_invalid()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\HttpException');

        $this->authenticator->provider = 'invalid_provider';

        // ASSERT:      Exception is thrown
        $this->authenticator->validateProvider();
    }

    /**
     * @test
     */
    public function it_can_validate_an_valid_provider()
    {
        // SETUP:      Provider is configured
        $provider = ProvidersTest::setupValidProvider();

        $this->authenticator->provider = $provider;

        $this->assertInstanceOf(Authenticator::class, $this->authenticator->validateProvider());
    }
}
