<?php

namespace Modules\Article\Entities;

use Fynduck\LaravelSearchable\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Article\Entities\ArticleTrans
 *
 * @property int $id
 * @property int $article_id
 * @property int $lang_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $short_desc
 * @property string|null $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property int $active
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans active()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans lang()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans searchRestricted($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereLangId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereShortDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTrans whereTitle($value)
 * @mixin \Eloquent
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
                'slug'        => 9,
                'description' => 9,
                'short_desc'  => 8,
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
            'slug',
            'article_id',
            'lang_id',
            'active'
        ];
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
