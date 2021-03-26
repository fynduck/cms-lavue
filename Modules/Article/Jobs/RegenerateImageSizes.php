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
use Modules\Article\Services\ArticleService;

class RegenerateImageSizes implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param ArticleService $articleService
     * @return void
     */
    public function handle(ArticleService $articleService)
    {
        $imageSettings = ArticleSettings::where('name', 'sizes')->first();
        if ($imageSettings) {
            $data = $articleService->prepareImgParams($imageSettings);
            $articles = Article::where('image', '!=', '')->get(['image']);
            foreach ($articles as $article) {
                if ($article->image) {
                    $articleService->deleteImages($article->image);
                    $imageName = $articleService->getOriginalImageName($article->image);
                    $path = Storage::get(Article::FOLDER_IMG . '/' . $imageName);
                    $articleService->generateImageSizes($path, $data, $imageName);

                    $articleService->deleteImages($imageName);
                    $articleService->generateReserveImg($data, $imageName, false);
                }
            }
        }
    }
}
