<?php

namespace Modules\Article\Jobs;

use Fynduck\FilesUpload\ManipulationImage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;

class GenerateImages implements ShouldQueue
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
            $this->generateArticleImages($this->article);

        } else {
            $articles = Article::where('image', '!=', '')->get();

            foreach ($articles as $article) {
                $this->generateArticleImages($article);
            }
        }
    }

    private function generateArticleImages(Article $article)
    {
        $sizes = ArticleSettings::where('name', 'sizes')->first();

        if ($sizes->data['sizes']) {
            $resizeMethod = !empty($sizes->data['action']) ? $sizes->data['action'] : 'resize';
            $greyscale = !empty($sizes->data['greyscale']) ? $sizes->data['greyscale'] : false;
            $blur = !empty($sizes->data['blur']) ? $sizes->data['blur'] : null;
            $brightness = !empty($sizes->data['brightness']) ? $sizes->data['brightness'] : 0;
            $background = !empty($sizes->data['background']) ? $sizes->data['background'] : null;
            $path = Storage::get(Article::FOLDER_IMG . '/' . $article->image);
            ManipulationImage::load($path)
                ->setSizes($sizes->data['sizes'])
                ->setName($article->image)
                ->setFolder(Article::FOLDER_IMG)
                ->setGreyscale($greyscale)
                ->setBlur($blur)
                ->setBrightness($brightness)
                ->setBackground($background)
                ->save($resizeMethod);
        }
    }
}
