<?php

namespace Modules\Banner\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Banner\Entities\BannerTrans
 *
 * @property int $id
 * @property int $banner_id
 * @property string|null $title
 * @property string|null $description
 * @property int $lang
 * @property int $status
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans whereBannerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Banner\Entities\BannerTrans whereTitle($value)
 * @mixin \Eloquent
 */
class BannerTrans extends Model
{
    protected $table = 'banner_trans';

    protected $fillable = ['banner_id', 'title', 'description', 'lang_id', 'active'];

    public $timestamps = false;

    /**
     * Select translation by id
     * @return array
     */
    static function getTrans($id)
    {
        return BannerTrans::where('banner_id', $id)->get();
    }

    /**
     * Get all status
     * @param $id
     * @return mixed
     */
    static function getStatusLang($id)
    {
        return BannerTrans::where('banner_id', $id)->pluck('status', 'lang');
    }
}
