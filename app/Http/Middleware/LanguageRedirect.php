<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;

class LanguageRedirect
{

    public function __construct(Application $app, Redirector $redirector, Request $request)
    {
        $this->app = $app;
        $this->redirector = $redirector;
        $this->request = $request;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $isPost = $request->isMethod('post');
        $isAjax = $request->ajax();
        $isFm = $request->is('admin/filemanager*');
        $enableLang = checkModule('Language');
        if ($isPost || $isAjax || $isFm || !$enableLang)
            return $next($request);

        $languages = Cache::remember('languages', now()->addDay(), function () {
            return Language::where('active', 1)
                ->get(['id', 'slug', 'country_iso', 'default', 'name', 'image']);
        });

        // Make sure current locale exists.
        $urlLocale = $request->segment(1);
        $segments = $request->segments();

        if (is_null($urlLocale) && count($languages) > 1) {
            $segments[0] = config('app.fallback_locale');

            return $this->redirector->to(implode('/', $segments));
        }

        $currLang = Language::where('slug', $urlLocale)
            ->where('active', 1)
            ->first(['id', 'slug', 'default']);
        if (is_null($currLang)) {
            if (count($languages) > 1) {
                foreach ($languages as $item) {
                    if ($item->default == 1) {
                        array_unshift($segments, $item->slug);

                        return $this->redirector->to(implode('/', $segments));
                    }
                }
            }
        } elseif (count($languages) == 1) {
            unset($segments[array_search($currLang->slug, $segments)]);

            return $this->redirector->to(implode('/', $segments));
        }

        $locales = [];
        $locale = $fallback = false;
        foreach ($languages as $lang) {
            if ($lang->slug == $urlLocale)
                $locale = $lang;
            $locales[$lang->id] = $lang;
            if ($lang->default == 1)
                $fallback = $lang;
        }

        if (!$locale) {
            if ($fallback)
                $locale = $fallback;
            else
                $locale = $fallback = reset($locales);
        }

        $this->app->config->set('app.fallback_locale', $fallback->slug);
        $this->app->config->set('app.fallback_locale_id', $fallback->id);
        $this->app->setLocale($locale->slug);
        $this->app->config->set('app.locale_id', $locale->id);
        if (config('app.fallback_locale') != $fallback->slug)
            $this->app->config->set('app.locale_prefix', $locale->slug);

        $this->app->config->set('app.locales', $locales);

        return $next($request);
    }

}
