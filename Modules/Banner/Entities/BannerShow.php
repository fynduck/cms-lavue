<?php

namespace Modules\Banner\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Banner\Entities\BannerShow
 *
 * @property int $id
 * @property int $banner_id
 * @property int $page_id
 * @property string $type_page
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerShow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerShow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerShow query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerShow whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerShow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerShow wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerShow whereTypePage($value)
 * @mixin \Eloquent
 */
class BannerShow extends Model
{
    protected $table = 'banner_show';

    protected $fillable = ['banner_id', 'type_page', 'page_id'];

    public $timestamps = false;

    /**
     * Select pages show banner
     * @return array
     */
    static function get($id)
    {
        return BannerShow::where('banner_id', $id)->get();
    }

    /**
     * Select pages show for all banners
     * @return array
     */
    static function getAll()
    {
        return BannerShow::all();
    }
}
