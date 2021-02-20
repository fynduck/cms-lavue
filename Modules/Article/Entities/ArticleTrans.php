<?php

namespace Modules\Article\Entities;

use Fynduck\LaravelSearchable\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Article\Entities\ArticleTrans
 *
 * @property int $id
 * @property int $article_id
 * @property int $lang
 * @property string|null $title
 * @property string|null $description
 * @property string|null $short_desc
 * @property string $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property int $active
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans active()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans lang()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans searchRestricted($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereShortDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereTitle($value)
 * @mixin \Eloquent
 * @property int $lang_id
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleTrans whereLangId($value)
 */
class ArticleTrans extends Model
{
    use Searchable;

    protected $fillable = [
        'article_id',
        'slug',
        'title',
        'description',
        'short_desc',
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
    protected function toSearchableArray()
    {
        return [
            'columns' => [
                'title'       => 10,
                'short_desc'  => 7,
                'description' => 7,
                'slug'        => 5,
            ]
        ];
    }

    /**
     * Select fields
     * @return array
     */
    public function selectFields()
    {
        return [
            'title',
            'description',
            'short_desc',
            'article_id',
            'slug',
            'lang_id',
            'active'
        ];
    }

    /**
     * Select title by id and lang_id
     * @param $id
     * @param null $active
     * @param null $lang_id
     * @return array
     */
    static function getTitle($id, $active = null, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;
        $query = ArticleTrans::where('lang_id', $lang_id);
        if ($active) {
            $query->where('active', 1);
        }
        if (is_array($id)) {
            $query->whereIn('article_id', $id);
        } else {
            $query->where('article_id', $id);
        }
        $query->select('title', 'article_id');

        return $query->pluck('title', 'article_id');
    }

    /**
     * Select slug by id and lang_id
     * @param $id
     * @param null $lang_id
     * @param null $active
     * @return array
     */
    static function getSlug($id, $active = null, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;
        $query = ArticleTrans::where('lang_id', $lang_id);
        if ($active) {
            $query->where('active', 1);
        }
        if (is_array($id)) {
            $query->whereIn('article_id', $id);
        } else {
            $query->where('article_id', $id);
        }

        return $query->pluck('slug', 'article_id');
    }

    /**
     * Select slugs item
     * @return array
     */
    static function getSlugs($id)
    {
        return ArticleTrans::where('article_id', $id)->where('active', 1)->pluck('slug', 'lang_id');
    }

    public function getArticle()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function scopeLang($query)
    {
        return $query->where('lang_id', config('app.locale_id'));
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
