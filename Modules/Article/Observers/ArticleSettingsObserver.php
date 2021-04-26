<?php

namespace Modules\Article\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\ArticleSettings;

class ArticleSettingsObserver
{
    /**
     * Handle the article "created" event.
     *
     * @param ArticleSettings $articleSettings
     * @return void
     */
    public function saved(ArticleSettings $articleSettings)
    {
        Cache::forget($articleSettings->name);
        Cache::forget('article_' . $articleSettings->name);
    }

    /**
     * Handle the article "deleted" event.
     *
     * @param ArticleSettings $articleSettings
     * @return void
     */
    public function deleted(ArticleSettings $articleSettings)
    {
        Cache::forget($articleSettings->name);
        Cache::forget('article_' . $articleSettings->name);
    }
}
