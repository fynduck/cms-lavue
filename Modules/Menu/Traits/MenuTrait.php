<?php

namespace Modules\Menu\Traits;

use Illuminate\Http\Request;
use Modules\Menu\Entities\Menu;

trait MenuTrait
{
    /**
     * Get menus by position
     * @position $position
     * @param $position
     * @param null $active
     * @param null $lang_id
     * @return mixed
     */
    static function getMenuByPosition($position = null, $active = null, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;

        $query = Menu::leftJoin('menu_trans', 'menus.id', '=', 'menu_trans.menu_id')
            ->where('lang_id', $lang_id)
            ->whereIn('position', (array)$position);
        if ($active) {
            $query->where('active', $active);
        }

        return $query->whereNull('parent_id')->orderBy('priority')->orderBy('updated_at', 'DESC');
    }

    /**
     * Filter list menu
     * @param $query
     * @param Request $request
     */
    public function scopeFilter($query, Request $request)
    {
        if ($request->get('q')) {
            $query->where(
                function ($q) use ($request) {
                    $q->where('title', 'LIKE', '%' . $request->get('q') . '%')
                        ->orWhere('description', 'LIKE', '%' . $request->get('q') . '%');
                }
            );
        }

        if ($request->get('active')) {
            $query->where('active', true);
        }

        if ($request->get('lang_id')) {
            $query->where('lang_id', $request->get('lang_id'));
        }

        if ($request->get('sortBy')) {
            $query->orderBy($request->get('sortBy'), $request->get('sortDesc', 'ASC'));
        }
    }

    public static function getSizes(): array
    {
        return [
            'xs' => ['width' => 60, 'height' => 60],
            'sm' => ['width' => 500, 'height' => 500],
        ];
    }

    /**
     * @return array
     */
    public static function positions(): array
    {
        return [
            self::TOP_MENU     => trans('menu::admin.top_menu'),
            //            self::MAIN_MENU => trans('menu::admin.main_menu'),
            self::CUSTOM_MENU  => trans('menu::admin.custom_menu'),
            self::FOOTER_LINKS => trans('menu::admin.footer_menus')
        ];
    }

    /**
     * @return string[]
     */
    public static function targets(): array
    {
        return [
            '_self'   => '_self',
            '_blank'  => '_blank',
            '_parent' => '_parent',
            '_top'    => '_top'
        ];
    }
}