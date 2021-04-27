<?php

namespace Modules\Menu\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Menu\Entities\MenuSettings;

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
        Cache::forget("menu_{$menuSettings->name}sizes");
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
        Cache::forget("menu_{$menuSettings->name}sizes");
        Cache::forget('menu_sizes');
    }
}
