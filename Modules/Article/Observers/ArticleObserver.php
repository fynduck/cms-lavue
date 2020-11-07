<?php

namespace Modules\Article\Observers;

use Fynduck\FilesUpload\PrepareFile;
use Modules\Article\Entities\Article;

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
        \Cache::forget('home_article');
        \Cache::forget('pluck_promotions');
    }

    /**
     * Handle the article "deleted" event.
     *
     * @param Article $article
     * @return void
     */
    public function deleted(Article $article)
    {

        PrepareFile::deleteImages(Article::FOLDER_IMG, $article->icon, Article::getSizes());
        PrepareFile::deleteImages(Article::FOLDER_IMG, $article->image, Article::getSizes());
        \Cache::forget('home_article');
        \Cache::forget('pluck_promotions');
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
