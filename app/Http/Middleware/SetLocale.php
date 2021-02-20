<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;

class SetLocale
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!checkModule('Language')) {
            return $next($request);
        }

        $languages = Cache::remember(
            'languages',
            now()->addDay(),
            function () {
                return Language::where('active', 1)
                    ->get(['id', 'slug', 'country_iso', 'default', 'name', 'image']);
            }
        );

        $preferLangSlug = null;
        if (!$request->cookie('prefer_lang') && !$request->get('locale_id')) {
            $acceptLang = $request->header('accept-language');

            if (is_null($preferLangSlug) && $acceptLang) {
                $clientLanguages = explode(',', $acceptLang);

                if (count($clientLanguages)) {
                    foreach ($clientLanguages as $clientLangSlug) {
                        $preferLangSlug = Language::where('slug', substr($clientLangSlug, 0, 2))->where('active', 1)->value(
                            'slug'
                        );
                        if ($preferLangSlug) {
                            break;
                        }
                    }
                }
            }
        } else {
            if ($request->get('locale_id')) {
                $preferLangSlug = Language::whereKey($request->get('locale_id'))->where('active', 1)->value('slug');
            } else {
                $preferLangSlug = $request->cookie('prefer_lang');
            }
        }

        if (is_null($preferLangSlug)) {
            $preferLangSlug = Language::where('default', 1)->where('active', 1)->value('slug');
        }

        $locales = [];
        $locale = null;
        foreach ($languages as $lang) {
            if ($lang->slug == $preferLangSlug) {
                $locale = $lang;
            }

            $locales[$lang->id] = $lang;
            if ($lang->default == 1) {
                $fallback = $lang;
            }
        }
        if ($locale) {
            $this->app->setLocale($locale->slug);
            $this->app->config->set('app.locale_id', $locale->id);
        }

        if (config('app.fallback_locale') != $fallback->slug) {
            $this->app->config->set('app.locale_prefix', $fallback->slug);
        }

        $this->app->config->set('app.locales', $locales);

        $faker_locale = strtolower($locale->slug) . '_';
        if ($locale->country_iso) {
            $faker_locale .= strtoupper($locale->country_iso);
        } else {
            $faker_locale .= strtoupper($locale->slug);
        }

        $this->app->config->set('app.faker_locale', $faker_locale);
        setlocale(LC_ALL, $faker_locale . '.utf8');

        $this->app->config->set('app.fallback_locale', $fallback->slug);
        $this->app->config->set('app.fallback_locale_id', $fallback->id);

        if (!in_array($request->getMethod(), ['POST', 'PUT'])) {
            return $next($request)->cookie(
                'prefer_lang',
                $preferLangSlug,
                now()->addYear()->diffInMinutes(),
                null,
                null,
                config('app.ssl'),
                false
            );
        }

        return $next($request);
    }
}
