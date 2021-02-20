<?php

namespace Modules\Article\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Modules\Article\Entities\Article;

class DeleteImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $article;

    /**
     * Create a new job instance.
     *
     * @param Article|null $article
     */
    public function __construct(Article $article = null)
    {
        $this->article = $article;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->article) {
            $this->deleteArticleImages($this->article);

            Storage::delete(Article::FOLDER_IMG . '/' . $this->article->image);
        } else {
            $articles = Article::where('image', '!=', '')->get();

            foreach ($articles as $article) {
                $this->deleteArticleImages($article);
            }
        }
    }

    private function deleteArticleImages(Article $article)
    {
        $directories = Storage::directories(Article::FOLDER_IMG);
        foreach ($directories as $directory) {
            Storage::delete($directory . '/' . $article->image);
        }
    }
}
