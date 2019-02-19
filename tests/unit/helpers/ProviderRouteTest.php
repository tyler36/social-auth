<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tyler36\SocialAuth\Helpers\ProviderRoute;
use Tests\TestCase;

/**
 * Class ProviderRouteTest
 *
 * @test
 * @group route
 * @group unit
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ProviderRouteTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_false_if_route_is_null()
    {
        $this->assertNull(Route::current());
        $this->assertFalse(ProviderRoute::get());
    }

    /**
     * @test
     */
    public function it_can_get_provider_from_route_parameters()
    {
        Route::shouldReceive('current->parameters')
            ->andReturn(['provider' => 'github']);

        $this->assertSame('github', ProviderRoute::get());
    }
}
