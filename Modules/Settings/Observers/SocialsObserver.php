<?php

namespace Modules\Settings\Observers;

use Modules\Article\Entities\Article;
use Modules\Settings\Entities\Pagination;
use Modules\Settings\Entities\Social;

class SocialsObserver
{
    /**
     * Handle the user "saved" event.
     *
     * @param Social $social
     * @return void
     */
    public function saved(Social $social)
    {
        \Cache::forget('socials');
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param Social $social
     * @return void
     */
    public function deleted(Social $social)
    {
        \Cache::forget('socials');
    }
}
