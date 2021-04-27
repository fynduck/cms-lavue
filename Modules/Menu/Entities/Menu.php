<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Menu\Traits\MenuTrait;

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
 * @property int $priority
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
 * @property string|null $attributes
 * @method static \Illuminate\Database\Eloquent\Builder|Menu priority()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu wherePriority($value)
 */
class Menu extends Model
{
    use MenuTrait;

    public const FOLDER_IMG = 'menus';

    public const TOP_MENU = 'top_menu';
    public const MAIN_MENU = 'home_menu';
    public const CUSTOM_MENU = 'custom_menu';
    public const FOOTER_LINKS = 'footer_links';

    public const FORMATS = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

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
        'priority',
        'nofollow',
        'attributes'
    ];

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
            ->whereHas(
                'getTrans',
                function ($query) {
                    $query->where('lang_id', config('app.locale_id'))
                        ->where('active', 1);
                }
            );
    }

    public function scopePriority($query)
    {
        return $query->orderBy('priority');
    }
}
