<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Class SocialAuthServiceProvider
 *
 * @test
 * @group unit
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class SocialAuthServiceProviderTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        $this->vendorPath = __DIR__.'/../../src/vendor';
    }

    /**
     * @test
     */
    public function it_contains_routing_vendor_file()
    {
        $this->assertFileExists("{$this->vendorPath}/routes/web.php");
        $this->assertFileExists("{$this->vendorPath}/controller/SocialAuthController.php");
    }

    /**
     * @test
     */
    public function it_contains_configuration_vendor_file()
    {
        $this->assertFileExists("{$this->vendorPath}/config/socialauth.php");
    }

    /**
     * @test
     */
    public function it_contains_lang_vendor_files()
    {
        $this->assertFileExists("{$this->vendorPath}/lang/en/message.php");
    }

    /**
     * @test
     */
    public function it_contains_view_vendor_files()
    {
        $this->assertFileExists("{$this->vendorPath}/views/login.blade.php");
    }

    /**
     * @test
     */
    public function it_contains_migration_files()
    {
        $this->assertFileExists("{$this->vendorPath}/migrations/update_user_model.php");
    }
}
