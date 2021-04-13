<?php

namespace Modules\Page\Entities;

use Fynduck\LaravelSearchable\Searchable;
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
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans active()
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans lang(?int $lang_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans pageId(int $page_id)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans query()
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans searchRestricted($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereDescriptionFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereLangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTrans whereTitle($value)
 * @mixin \Eloquent
 */
class PageTrans extends Model
{
    use Searchable;

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
     * Searchable rules.
     * Columns and their priority in search results.
     * Columns with higher values are more important.
     * Columns with equal values have equal importance.
     * @return array
     * @var array
     */
    protected function toSearchableArray(): array
    {
        return [
            'columns' => [
                'title'              => 10,
                'slug'               => 9,
                'description'        => 9,
                'description_footer' => 8,
            ]
        ];
    }

    /**
     * Select fields
     * @return array
     */
    public function selectFields(): array
    {
        return [
            'title',
            'slug',
            'description',
            'description_footer as short_desc',
            'lang_id',
            'active',
        ];
    }

    public function scopePageId($query, int $page_id)
    {
        return $query->where('page_id', $page_id);
    }

    public function scopeLang($query, int $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');

        return $query->where('lang_id', $lang_id);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
