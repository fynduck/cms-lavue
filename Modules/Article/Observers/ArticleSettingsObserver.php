<?php

namespace Modules\Article\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\ArticleSettings;
use Modules\Article\Jobs\RegenerateImageSizes;

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
        RegenerateImageSizes::dispatch();
        Cache::forget('article_sizes');
    }

    /**
     * Handle the article "deleted" event.
     *
     * @param ArticleSettings $articleSettings
     * @return void
     */
    public function deleted(ArticleSettings $articleSettings)
    {
        Cache::forget('article_sizes');
    }
}
