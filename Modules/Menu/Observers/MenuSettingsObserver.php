<?php

namespace Modules\Menu\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Entities\MenuSettings;
use Modules\Menu\Jobs\DeleteImages;
use Modules\Menu\Jobs\GenerateImages;

class MenuSettingsObserver
{

    /**
     * Handle the menu "saved" event.
     *
     * @param MenuSettings $menuSettings
     * @return void
     */
    public function saved(MenuSettings $menuSettings)
    {
        DeleteImages::dispatch();
        GenerateImages::dispatch();
        Cache::forget('menu_sizes');
    }

    /**
     * Handle the menu "deleted" event.
     *
     * @param MenuSettings $menuSettings
     * @return void
     */
    public function deleted(MenuSettings $menuSettings)
    {
        Cache::forget('menu_sizes');
    }
}
