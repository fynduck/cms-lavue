<?php

namespace Modules\Page\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Page\Entities\Page;

class PageObserver
{
    /**
     * Handle the page "saved" event.
     *
     * @param Page $page
     * @return void
     */
    public function saved(Page $page)
    {
        Cache::forget('urls_pages_' . config('app.locale_id'));
        Cache::forget('home_page_' . config('app.locale_id'));
    }

    /**
     * Handle the page "deleted" event.
     *
     * @param Page $page
     * @return void
     */
    public function deleted(Page $page)
    {
        Cache::forget('urls_pages_' . config('app.locale_id'));
        Cache::forget('home_page_' . config('app.locale_id'));
    }
}
