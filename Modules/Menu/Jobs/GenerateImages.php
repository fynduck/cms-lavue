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

class GenerateImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $menu;

    /**
     * Create a new job instance.
     *
     * @param Menu|null $menu
     */
    public function __construct(Menu $menu = null)
    {
        $this->menu = $menu;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->menu) {
            $this->generateMenuImages($this->menu);
        } else {
            $menus = Menu::where('image', '!=', '')->get();

            foreach ($menus as $menu) {
                $this->generateMenuImages($menu);
            }
        }
    }

    private function generateMenuImages(Menu $menu)
    {
        $sizes = MenuSettings::where('name', 'sizes')->first();

        if ($sizes->data['sizes']) {
            $resizeMethod = !empty($sizes->data['action']) ? $sizes->data['action'] : 'resize';
            $greyscale = !empty($sizes->data['greyscale']) ? $sizes->data['greyscale'] : false;
            $blur = !empty($sizes->data['blur']) ? $sizes->data['blur'] : null;
            $brightness = !empty($sizes->data['brightness']) ? $sizes->data['brightness'] : 0;
            $background = !empty($sizes->data['background']) ? $sizes->data['background'] : null;
            $path = Storage::get(Menu::FOLDER_IMG . '/' . $menu->image);
            $optimize = !empty($sizes->data['optimize']) ? $sizes->data['optimize'] : false;
            ManipulationImage::load($path)
                ->setSizes($sizes->data['sizes'])
                ->setName($menu->image)
                ->setFolder(Menu::FOLDER_IMG)
                ->setGreyscale($greyscale)
                ->setBlur($blur)
                ->setBrightness($brightness)
                ->setBackground($background)
                ->setOptimize($optimize)
                ->save($resizeMethod);
        }
    }
}
