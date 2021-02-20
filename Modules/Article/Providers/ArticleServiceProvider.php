<?php

namespace Modules\Article\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;
use Modules\Article\Observers\ArticleObserver;
use Modules\Article\Observers\ArticleSettingsObserver;

class ArticleServiceProvider extends ServiceProvider
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

        Validator::extendImplicit(
            'article_image',
            function ($attribute, $value, $parameters, $validator) {
                $checkType = in_array(request()->get('type'), [Article::ARTICLES, Article::NEWS]);
                if (!$value && $checkType && !request()->get('old_image')) {
                    return false;
                }

                return true;
            },
            'Поле :attribute обязательно для заполнения.'
        );

        Article::observe(ArticleObserver::class);
        ArticleSettings::observe(ArticleSettingsObserver::class);
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
                __DIR__ . '/../Config/config.php' => config_path('article.php'),
            ],
            'config'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'article'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/article');

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
                        return $path . '/modules/article';
                    },
                    \Config::get('view.paths')
                ),
                [$sourcePath]
            ),
            'article'
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/article');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'article');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'article');
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
