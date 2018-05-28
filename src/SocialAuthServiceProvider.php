<?php

namespace Tyler36\SocialAuth;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Tyler36\SocialAuth\Helpers\ProvidersViewComposer;

class SocialAuthServiceProvider extends ServiceProvider
{
    public static $namespace = 'socialauth';

    protected static $vendorPath  = __DIR__.'/vendor';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootConfig()
            ->bootRoutes()
            ->bootTranslations()
            ->bootViews()
            ->bootMigrations();
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerViewComposer();
    }

    /**
     * Automatically inject data into blade component
     *
     * @return void
     */
    public function registerViewComposer()
    {
        View::composer(['vendor.socialauth.login'], ProvidersViewComposer::class);
    }

    /**
     * Initialize Configuration
     *
     * @return void
     */
    public function bootConfig()
    {
        $this->publishes([self::$vendorPath.'/config/socialauth.php' => config_path(self::$namespace.'.php')]);

        return $this;
    }

    /**
     * Initialize Translations
     *
     * @return void
     */
    public function bootMigrations()
    {
        $this->loadMigrationsFrom(self::$vendorPath.'/migrations/add_provider_login_details.php');

        // $this->publishes([
        //     self::$vendorPath .'/migrations/add_provider_login_details.php' =>
        //     database_path('migrations/'. date('Y_m_d_His') .'_add_provider_login_details.php')
        // ]);

        return $this;
    }

    /**
     * Initialize Routes
     *
     * @return void
     */
    public function bootRoutes()
    {
        $this->publishes([self::$vendorPath.'/routes/web.php' => base_path('routes') . '/' .self::$namespace.'.php']);

        return $this->copyController();
    }

    /**
     * Copy controller to App
     *
     * @return void
     */
    public function copyController()
    {
        $this->publishes([
            self::$vendorPath.'/controller/SocialAuthController.php' => app_path('http/Controllers/Auth/SocialAuthController.php'),
        ]);

        return $this;
    }

    /**
     * Initialize Translations
     *
     * @return void
     */
    public function bootTranslations()
    {
        $this->loadTranslationsFrom(self::$vendorPath.'/lang', self::$namespace);
        $this->publishes([self::$vendorPath.'/lang' => resource_path('lang/vendor/'.self::$namespace)]);

        return $this;
    }

    /**
     * Initialize Views
     *
     * @return void
     */
    public function bootViews()
    {
        $this->loadViewsFrom(self::$vendorPath.'/views', self::$namespace);
        $this->publishes([self::$vendorPath.'/views' => resource_path('views/vendor/'.self::$namespace)]);

        return $this;
    }
}
