<?php

namespace Modules\Article\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\ArticleSettings;

class ArticleSettingsObserver
{
    /**
     * Handle the article "created" event.
     *
     * @return void
     */
    public function saved()
    {
        Cache::forget('article_settings');
    }

    /**
     * Handle the article "deleted" event.
     *
     * @param ArticleSettings $articleSettings
     * @return void
     */
    public function deleted(ArticleSettings $articleSettings)
    {
        Cache::forget('article_settings');
    }
}
