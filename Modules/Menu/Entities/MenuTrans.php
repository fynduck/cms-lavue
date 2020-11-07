<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Menu\Entities\MenuTrans
 *
 * @property int $id
 * @property int $menu_id
 * @property int $lang_id
 * @property string|null $title
 * @property string|null $additional_title
 * @property string|null $link
 * @property string|null $description
 * @property int $active
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans lang()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereAdditionalTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereLangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Menu\Entities\MenuTrans whereTitle($value)
 * @mixin \Eloquent
 */
class MenuTrans extends Model
{
    protected $fillable = [
        'menu_id',
        'title',
        'additional_title',
        'description',
        'link',
        'active',
        'lang_id'
    ];

    protected $hidden = ['id'];

    public $timestamps = false;

    public function scopeLang($query)
    {
        return $query->where('lang_id', config('app.locale_id'));
    }
}
