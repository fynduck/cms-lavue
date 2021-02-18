<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;
use Modules\Page\Entities\Page;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        /**
         * urls pages
         */
        foreach (Language::active()->get() as $language) {
            Cache::remember('urls_pages_' . $language->id, now()->addHours(5), function () use ($language) {
                return Page::getSlugAllStaticPages($language->id)->toArray();
            });
        }
    }
}
