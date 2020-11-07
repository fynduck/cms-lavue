<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Article\Entities\ArticleViews
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleViews newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleViews newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleViews query()
 * @mixin \Eloquent
 */
class ArticleViews extends Model
{
    protected $fillable = ['ip_id', 'session_id', 'user_agent', 'type', 'article_id', 'views'];
}
