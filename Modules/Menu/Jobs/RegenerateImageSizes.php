<?php

namespace Modules\Menu\Jobs;

use Fynduck\FilesUpload\ManipulationImage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Entities\MenuSettings;
use Modules\Menu\Services\MenuService;

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
     * @param MenuService $menuService
     * @return void
     */
    public function handle(MenuService $menuService)
    {
        $imageSettings = MenuSettings::where('name', 'sizes')->first();
        if ($imageSettings) {
            $data = $menuService->prepareImgParams($imageSettings);
            $menus = Menu::where('image', '!=', '')->get(['image']);
            foreach ($menus as $menu) {
                $menuService->deleteImages($menu->image);
                if ($menu->image) {
                    $path = Storage::get(Menu::FOLDER_IMG . '/' . $menu->image);
                    $this->generateBannerImages($path, $data, $menu->image);
                }
            }
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $image
     */
    private function generateBannerImages(string $path, array $data, string $image)
    {
        ManipulationImage::load($path)
            ->setSizes($data['sizes'])
            ->setName($image)
            ->setFolder(Menu::FOLDER_IMG)
            ->setGreyscale($data['greyscale'])
            ->setBlur($data['blur'])
            ->setBrightness($data['brightness'])
            ->setBackground($data['background'])
            ->setOptimize($data['optimize'])
            ->save($data['resizeMethod']);
    }
}
