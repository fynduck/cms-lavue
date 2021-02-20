<?php

namespace Modules\Menu\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Menu\Entities\Menu;

class MenuObserver
{

    /**
     * Handle the menu "saved" event.
     *
     * @param Menu $menu
     * @return void
     */
    public function saved(Menu $menu)
    {
        foreach (config('app.locales') as $locale) {
            Cache::forget('site_menus_' . $locale->id);
            foreach (Menu::positions() as $position => $title) {
                Cache::forget("menu_{$position}_" . $locale->id);
            }
        }
    }

    /**
     * Handle the menu "deleted" event.
     *
     * @param Menu $menu
     * @return void
     */
    public function deleted(Menu $menu)
    {
        foreach (config('app.locales') as $locale) {
            Cache::forget('site_menus_' . $locale->id);
            foreach (Menu::positions() as $position => $title) {
                Cache::forget("menu_{$position}_" . $locale->id);
            }
        }
    }

    /**
     * Handle the menu "restored" event.
     *
     * @param Menu $menu
     * @return void
     */
    public function restored(Menu $menu)
    {
        //
    }

    /**
     * Handle the menu "force deleted" event.
     *
     * @param Menu $menu
     * @return void
     */
    public function forceDeleted(Menu $menu)
    {
        //
    }
}
