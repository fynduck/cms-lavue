<?php

namespace Modules\Banner\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Banner\Traits\BannerTrait;

/**
 * Modules\Banner\Entities\Banner
 *
 * @property int $id
 * @property int|null $page_id
 * @property string|null $image
 * @property int $sort
 * @property string $target
 * @property string $type_page
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $date_from
 * @property \Illuminate\Support\Carbon|null $date_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner betweenDate()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner filter(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereTypePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\Banner whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $mobile_image
 * @property string|null $link
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereMobileImage($value)
 * @property int $priority
 * @property string $position
 * @method static \Illuminate\Database\Eloquent\Builder|Banner wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner wherePriority($value)
 */
class Banner extends Model
{
    use BannerTrait;

    public const FOLDER_IMG = 'banners';

    public const TOP = 'top';

    public const CONTENT = 'content';

    public const FORMATS = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

    protected $fillable = [
        'page_id',
        'image',
        'mobile_image',
        'link',
        'priority',
        'target',
        'type_page',
        'position',
        'date_from',
        'date_to'
    ];

    protected $dates = [
        'date_from',
        'date_to'
    ];

    public function getTrans()
    {
        return $this->hasMany(BannerTrans::class, 'banner_id');
    }

    public function getShow()
    {
        return $this->hasMany(BannerShow::class, 'banner_id');
    }
}
