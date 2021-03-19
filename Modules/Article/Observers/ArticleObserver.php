<?php

namespace Modules\Article\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\Article;
use Modules\Article\Services\ArticleService;

class ArticleObserver
{
    /**
     * Handle the article "created" event.
     *
     * @param Article $article
     * @return void
     */
    public function saved(Article $article)
    {
        Cache::forget('home_article');
        Cache::forget('pluck_promotions');
    }

    /**
     * Handle the article "deleted" event.
     *
     * @param Article $article
     * @return void
     */
    public function deleted(Article $article)
    {
        $articleService = new ArticleService();
        if ($article->image) {
            $articleService->deleteImages($article->image);
        }

        Cache::forget('home_article');
        Cache::forget('pluck_promotions');
    }

    /**
     * Handle the article "restored" event.
     *
     * @param Article $article
     * @return void
     */
    public function restored(Article $article)
    {
        //
    }

    /**
     * Handle the article "force deleted" event.
     *
     * @param Article $article
     * @return void
     */
    public function forceDeleted(Article $article)
    {
        //
    }
}
