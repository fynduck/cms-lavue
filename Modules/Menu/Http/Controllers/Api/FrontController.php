<?php

namespace Modules\Menu\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Transformers\MenuResource;

class FrontController extends Controller
{
    public function getMenu(Request $request)
    {
        $position = $request->get('position', Menu::CATALOG_MENU);
        if (!array_key_exists($position, Menu::positions()))
            $position = 'none';

        $menus = Cache::remember("menu_{$position}_" . config('app.locale_id'), now()->addDay(), function () use ($request, $position) {

            $query = Menu::getMenuByPosition($position, 1);

            if ($request->get('page_id') && !in_array($position, [Menu::CATALOG_MENU, Menu::TOP_MENU])) {
                $query->whereHas('getShow', function ($query) use ($request) {
                    $query->where('show_type', $request->get('type'))
                        ->where('show_on', $request->get('page_id'));
                });
            }

            return $query->get(['menus.*', 'menu_trans.title', 'menu_trans.link']);
        });

        $additional = [];

        return MenuResource::collection($menus)->additional($additional);
    }
}