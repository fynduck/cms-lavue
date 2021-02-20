<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Menu\Entities\MenuShow
 *
 * @property int $id
 * @property int $menu_id
 * @property int $show_on
 * @property string $show_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Menu\Entities\Menu $menu
 * @property-read \Modules\Menu\Entities\MenuTrans $trans
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow whereShowOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow whereShowType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuShow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MenuShow extends Model
{
    protected $fillable = [
        'menu_id',
        'show_on',
        'show_type'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function trans()
    {
        return $this->belongsTo(MenuTrans::class, 'menu_id', 'menu_id');
    }
}
