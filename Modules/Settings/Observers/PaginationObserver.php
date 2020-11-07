<?php

namespace Modules\Settings\Observers;

use Modules\Article\Entities\Article;
use Modules\Settings\Entities\Pagination;

class PaginationObserver
{
    /**
     * Handle the user "saved" event.
     *
     * @param Pagination $pagination
     * @return void
     */
    public function saved(Pagination $pagination)
    {
//        \Cache::forget('manufacturers_page');
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param Pagination $pagination
     * @return void
     */
    public function deleted(Pagination $pagination)
    {
//        \Cache::forget('manufacturers_page');
    }
}
