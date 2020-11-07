<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Modules\Menu\Entities\Menu
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $position
 * @property string $type_page
 * @property int $page_id
 * @property string $style
 * @property string $target
 * @property string|null $image
 * @property string|null $icon
 * @property int $sort
 * @property int $nofollow
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Menu\Entities\Menu[] $children
 * @property-read int|null $children_count
 * @property-read \Modules\Menu\Entities\MenuTrans $trans
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu filter(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu sort()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereNofollow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereTypePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\Menu whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Menu\Entities\Menu[] $activeChildren
 * @property-read int|null $active_children_count
 */
class Menu extends Model
{
    const FOLDER_IMG = 'menus';

    const TOP_MENU = 'top_menu';
    const MAIN_MENU = 'home_menu';
    const CATALOG_MENU = 'catalog_menu';
    const CATEGORY_MENU = 'category_menu';
    const CUSTOM_MENU = 'custom_menu';
    const FOOTER_LINKS = 'footer_links';

    protected $fillable = [
        'parent_id',
        'image',
        'icon',
        'position',
        'show_on',
        'show_on_type',
        'type_page',
        'page_id',
        'target',
        'sort',
        'nofollow',
        'style'
    ];

    public static function targets()
    {
        return [
            '_self'   => '_self',
            '_blank'  => '_blank',
            '_parent' => '_parent',
            '_top'    => '_top'
        ];
    }

    public static function getSizes()
    {
        return [
            'xs' => ['width' => 60, 'height' => 60],
            'sm' => ['width' => 500, 'height' => 500],
        ];
    }

    public static function positions()
    {
        return [
            self::TOP_MENU      => trans('menu::admin.top_menu'),
            self::CATALOG_MENU  => trans('menu::admin.catalog_menu'),
            self::CATEGORY_MENU => trans('menu::admin.category_menu'),
            //            self::MAIN_MENU => trans('menu::admin.main_menu'),
            self::CUSTOM_MENU   => trans('menu::admin.custom_menu'),
            self::FOOTER_LINKS  => trans('menu::admin.footer_menus')
        ];
    }

    /**
     * Get menu by id
     * @param $id
     * @param null $active
     * @param null $lang_id
     * @return mixed
     */
    static function menuById($id, $active = null, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;

        $query = Menu::leftJoin('menu_trans', 'menus.id', '=', 'menu_trans.menu_id')
            ->where('lang_id', $lang_id)
            ->where('menus.id', $id);
        if ($active)
            $query->where('active', $active);

        return $query->first();
    }

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
        if ($active)
            $query->where('active', $active);

        return $query->whereNull('parent_id')->orderBy('sort')->orderBy('updated_at', 'DESC');
    }

    /**
     * Get parents menus by position
     * @position $position
     * @param $position
     * @param int $id
     * @param null $active
     * @param null $lang_id
     * @return mixed
     */
    static function parentsByPosition($position, $id = null, $active = null, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;

        $query = Menu::leftJoin('menu_trans', 'menus.id', '=', 'menu_trans.menu_id')
            ->where('position', $position)
            ->where('parent_id', null)
            ->where('menus.id', '<>', $id)
            ->where('lang_id', $lang_id);
        if ($active)
            $query->where('active', $active);

        return $query->orderBy('sort', 'ASC')->orderBy('updated_at', 'DESC')->get();
    }

    public function scopeFilter($query, Request $request)
    {
        if ($request->get('q'))
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->get('q') . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->get('q') . '%');
            });

        if ($request->get('active'))
            $query->where('active', 1);

        if ($request->get('lang_id'))
            $query->where('lang_id', config('app.locale_id'));

        if ($request->get('sortBy')) {
            $sort = 'ASC';
            if ($request->get('sortDesc'))
                $sort = 'DESC';
            $query->orderBy($request->get('sortBy'), $sort);
        }
    }

    public function getTrans()
    {
        return $this->hasMany(MenuTrans::class, 'menu_id');
    }

    public function getShow()
    {
        return $this->hasMany(MenuShow::class, 'menu_id');
    }

    public function trans()
    {
        $lang_id = config('app.locale_id');

        return $this->hasOne(MenuTrans::class, 'menu_id')->where('lang_id', $lang_id)->where('active', 1);
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function activeChildren()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->whereHas('getTrans', function ($query) {
                $query->where('lang_id', config('app.locale_id'))
                    ->where('active', 1);
            });
    }

    public function scopeSort($query)
    {
        return $query->orderBy('sort');
    }
}
