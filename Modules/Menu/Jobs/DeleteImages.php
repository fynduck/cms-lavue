<?php

namespace Modules\Menu\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Modules\Menu\Entities\Menu;

class DeleteImages implements ShouldQueue
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
            $this->deleteMenuImages($this->menu);

            Storage::delete(Menu::FOLDER_IMG . '/' . $this->menu->image);

        } else {
            $menus = Menu::where('image', '!=', '')->get();

            foreach ($menus as $menu) {
                $this->deleteMenuImages($menu);
            }
        }
    }

    private function deleteMenuImages(Menu $menu)
    {
        $directories = Storage::directories(Menu::FOLDER_IMG);
        foreach ($directories as $directory) {
            Storage::delete($directory . '/' . $menu->image);
        }
    }
}
