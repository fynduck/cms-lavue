<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Modules\Language\Entities\Language;
use Modules\Page\Entities\Page;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (checkModule('Language') && checkModule('Page')) {
            /**
             * urls pages
             */
            foreach (Language::active()->get() as $language) {
                Cache::remember(
                    'urls_pages_' . $language->id,
                    now()->addHours(5),
                    function () use ($language) {
                        return Page::getSlugAllStaticPages($language->id)->toArray();
                    }
                );
            }
        }

        Schema::defaultStringLength(191);
    }
}
