<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Article\Traits\ArticleTrait;

/**
 * Modules\Article\Entities\Article
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon|null $date_from
 * @property \Illuminate\Support\Carbon|null $date_to
 * @property float|null $discount
 * @property string|null $image
 * @property string|null $icon
 * @property int $socials
 * @property int $priority
 * @property string $type
 * @property int|null $no_show_home
 * @property int $views
 * @property int|null $error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article betweenDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article discount()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article filter(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereNoShowHome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereSocials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\Article whereViews($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Article wherePriority($value)
 */
class Article extends Model
{
    use ArticleTrait;

    public const FOLDER_IMG = 'articles';

    public const NEWS = 'news';

    public const ARTICLES = 'articles';

    public const PROMOTIONS = 'promotions';

    public const FORMATS = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

    protected $fillable = [
        'date',
        'date_from',
        'date_to',
        'discount',
        'image',
        'socials',
        'priority',
        'type',
        'no_show_home',
    ];

    protected $casts = [
        'double'
    ];

    protected $dates = [
        'date',
        'date_from',
        'date_to'
    ];

    public function getTrans()
    {
        return $this->hasMany(ArticleTrans::class, 'article_id');
    }

    public function getViews($type)
    {
        return $this->hasMany(ArticleViews::class, 'article_id')->where('type', $type);
    }
}
