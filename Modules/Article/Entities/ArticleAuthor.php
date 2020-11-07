<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Article\Entities\ArticleAuthor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleAuthor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleAuthor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Article\Entities\ArticleAuthor query()
 * @mixin \Eloquent
 */
class ArticleAuthor extends Model
{
    protected $table = 'article_author';

    protected $fillable = ['article_id', 'user_id'];

    public $timestamps = false;
}
