<?php

namespace Modules\Page\Entities;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PageTrans $trans
 * @method static Builder|Page filter(Request $request)
 * @method static Builder|Page newModelQuery()
 * @method static Builder|Page newQuery()
 * @method static Builder|Page query()
 * @method static Builder|Page whereCreatedAt($value)
 * @method static Builder|Page whereId($value)
 * @method static Builder|Page whereMethod($value)
 * @method static Builder|Page whereModule($value)
 * @method static Builder|Page whereParentId($value)
 * @method static Builder|Page whereSocials($value)
 * @method static Builder|Page whereSqlProducts($value)
 * @method static Builder|Page whereUpdatedAt($value)
 * @mixin Eloquent
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

    public function getTrans(): HasMany
    {
        return $this->hasMany(PageTrans::class, 'page_id');
    }

    public function trans(): HasOne
    {
        return $this->hasOne(PageTrans::class)->where('lang_id', config('app.locale_id'));
    }
}
