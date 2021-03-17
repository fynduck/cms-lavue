<?php

namespace Modules\Banner\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Observers\BannerObserver;

class BannerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Banner::observe(BannerObserver::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
            [
                __DIR__ . '/../Config/config.php' => config_path('banner.php'),
            ],
            'config'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'banner'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/banner');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes(
            [
                $sourcePath => $viewPath
            ],
            'views'
        );

        $this->loadViewsFrom(
            array_merge(
                array_map(
                    function ($path) {
                        return $path . '/modules/banner';
                    },
                    \Config::get('view.paths')
                ),
                [$sourcePath]
            ),
            'banner'
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/banner');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'banner');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'banner');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
