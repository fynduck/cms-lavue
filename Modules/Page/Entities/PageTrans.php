<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Page\Entities\PageTrans
 *
 * @property int $id
 * @property int $page_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $description_footer
 * @property string|null $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property int|null $active
 * @property int $lang_id
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereDescriptionFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereLangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Page\Entities\PageTrans whereTitle($value)
 * @mixin \Eloquent
 */
class PageTrans extends Model
{
    protected $fillable = [
        'page_id',
        'title',
        'description',
        'description_footer',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'lang_id',
        'active'
    ];

    public $timestamps = false;

    /**
     * Select slugs
     * @param $id
     * @return array
     */
    static function getSlugs($id)
    {
        return PageTrans::where('page_id', $id)->where('active', 1)->pluck('slug', 'lang_id');
    }

    static function getByPageId($id, $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');

        return PageTrans::where('page_id', $id)->where('lang_id', $lang_id)->where('active', 1);
    }

    /**
     * Select translation by id
     * @param $id
     * @param bool|false $languagesActive
     * @return array
     */
    static function getTrans($id, $languagesActive = false)
    {
        $query = PageTrans::where('page_id', $id);
        if ($languagesActive)
            $query->whereIn('lang_id', array_keys(config('app.locales')));

        return $query->get();
    }

    /**
     * Select title by id and lang
     * @param $id
     * @param null $active
     * @param null $lang_id
     * @return array
     * @internal param $ids
     */
    static function getTitle($id, $active = null, $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');
        $query = PageTrans::where('lang_id', $lang_id);
        if ($active)
            $query->where('active', 1);
        if (is_array($id))
            $query->whereIn('page_id', $id);
        else
            $query->where('page_id', $id);
        $query->select('title', 'page_id');

        return $query->pluck('title', 'page_id');
    }

    /**
     * Select slug by id and lang
     * @param $id
     * @param null $active
     * @param null $lang_id
     * @return array
     * @internal param $ids
     */
    static function getSlug($id, $active = null, $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');
        $query = PageTrans::where('lang_id', $lang_id);
        if ($active)
            $query->where('active', 1);
        if (is_array($id))
            $query->whereIn('page_id', $id);
        else
            $query->where('page_id', $id);

        return $query->pluck('slug', 'page_id');
    }


    /**Get all active
     * @param $id
     * @return mixed
     */
    static function getActiveLang($id)
    {
        return PageTrans::where('page_id', $id)->pluck('active', 'lang_id');
    }
}
