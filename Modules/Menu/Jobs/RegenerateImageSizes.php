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

    protected $position;

    protected $settingsName;

    /**
     * Create a new job instance.
     */
    public function __construct(string $position, string $settingsName)
    {
        $this->position = $position;

        $this->settingsName = $settingsName;
    }

    /**
     * Execute the job.
     *
     * @param MenuService $menuService
     * @return void
     */
    public function handle(MenuService $menuService)
    {
        $imageSettings = MenuSettings::where('name', $this->settingsName)->first();
        if ($imageSettings) {
            $data = $menuService->prepareImgParams($imageSettings);
            $menus = Menu::where('position', $this->position)
                ->where('image', '!=', '')
                ->get(['id', 'image']);

            foreach ($menus as $menu) {
                if ($menu->image) {
                    $menuService->deleteImages($menu->image);
                    $imageName = $menuService->getOriginalImageName($menu->image);
                    $menuService->deleteImages($imageName);
                    $path = Storage::get(Menu::FOLDER_IMG . '/' . $imageName);
                    $sizeSaveName = $menuService->setExtensionByEncode($imageName, $data['encode']);
                    $menuService->generateImageSizes($path, $data, $sizeSaveName);

                    $menuService->generateReserveImg($data, $imageName, false);

                    if ($sizeSaveName !== $menu->image) {
                        $menu->image = $sizeSaveName;
                        $menu->save();
                    }
                }
            }
        }
    }
}
