<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Page\Traits\PageTrait;

/**
 * Modules\Page\Entities\Page
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $method
 * @property string|null $module
 * @property string|null $sql_products
 * @property int|null $socials
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Page\Entities\PageTrans $trans
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page filter(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereSocials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereSqlProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\Page whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Page extends Model
{
    use PageTrait;

    protected $fillable = [
        'socials',
        'module',
        'sql_products'
    ];

    protected $hidden = [
        'method'
    ];

    public function getTrans()
    {
        return $this->hasMany(PageTrans::class, 'page_id');
    }

    public function trans()
    {
        return $this->hasOne(PageTrans::class)->where('lang_id', config('app.locale_id'));
    }
}
