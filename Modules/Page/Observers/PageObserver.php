<?php

namespace Modules\Page\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;
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
        foreach (Language::active()->get() as $language) {
            Cache::forget('urls_pages_' . $language->id);
        }
    }

    /**
     * Handle the page "deleted" event.
     *
     * @param Page $page
     * @return void
     */
    public function deleted(Page $page)
    {
        foreach (Language::active()->get() as $language) {
            Cache::forget('urls_pages_' . $language->id);
        }
    }
}
