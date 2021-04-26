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

    protected $type;

    protected $settingsName;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, string $settingsName)
    {
        $this->type = $type;

        $this->settingsName = $settingsName;
    }

    /**
     * Execute the job.
     *
     * @param ArticleService $articleService
     * @return void
     */
    public function handle(ArticleService $articleService)
    {
        $imageSettings = ArticleSettings::where('name', $this->settingsName)->first();
        if ($imageSettings) {
            $data = $articleService->prepareImgParams($imageSettings);
            $articles = Article::where('type', $this->type)
                ->where('image', '!=', '')
                ->get(['id', 'image']);

            foreach ($articles as $article) {
                if ($article->image) {
                    $articleService->deleteImages($article->image);
                    $imageName = $articleService->getOriginalImageName($article->image);
                    $articleService->deleteImages($imageName);
                    $path = Storage::get(Article::FOLDER_IMG . '/' . $imageName);
                    $sizeSaveName = $articleService->setExtensionByEncode($imageName, $data['encode']);
                    $articleService->generateImageSizes($path, $data, $sizeSaveName);

                    $articleService->generateReserveImg($data, $imageName, false);

                    if ($sizeSaveName !== $article->image) {
                        $article->image = $sizeSaveName;
                        $article->save();
                    }
                }
            }
        }
    }
}
