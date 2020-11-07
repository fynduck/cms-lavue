<?php

namespace Modules\Article\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Article\Entities\Article;

trait ArticleTrait
{
    public static function getSizes()
    {
        return [
            '50' => ['width' => 50, 'height' => null],
            'xs' => ['width' => 450, 'height' => 236],
            'md' => ['width' => 600, 'height' => 315],
            'lg' => ['width' => 800, 'height' => 420],
            'xl' => ['width' => 1000, 'height' => 525],
        ];
    }

    public static function getTypes()
    {
        return [
            self::ARTICLES   => trans('article::admin.article'),
            self::PROMOTIONS => trans('article::admin.promotion')
        ];
    }

    /**
     * Filter for list
     * @param $query
     * @param Request $request
     */
    public function scopeFilter($query, Request $request)
    {
        if ($request->get('q'))
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->get('q') . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->get('q') . '%');
            });

        if ($request->get('active'))
            $query->where('active', 1);

        if ($request->get('lang_id'))
            $query->where('lang_id', config('app.locale_id'));

        if ($request->get('sortBy')) {
            $sort = 'ASC';
            if ($request->get('sortDesc'))
                $sort = 'DESC';
            $query->orderBy($request->get('sortBy'), $sort);
        }
    }

    /**
     * Select all items
     * @param bool|false $type
     * @param null $active
     * @param null $lang_id
     * @return mixed
     */
    static function getAll($type = false, $active = null, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;

        $query = Article::leftJoin('article_trans', 'articles.id', '=', 'article_trans.article_id')
            ->where('lang_id', $lang_id);

        if ($active)
            $query->where('active', $active);

        if ($type)
            $query->where('type', $type);

        return $query->orderBy('sort')->orderBy('date', 'DESC');
    }

    static function articles($type, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;

        return Article::leftJoin('article_trans', 'articles.id', '=', 'article_trans.article_id')
            ->where('type', $type)
            ->where('lang_id', $lang_id)
            ->where('active', 1);
    }

    /**
     * Select item by slug
     * @param $slug
     * @param null $lang_id
     * @return mixed
     */
    static function getItemBySlug($slug, $lang_id = null)
    {
        $lang_id = is_null($lang_id) ? config('app.locale_id') : $lang_id;

        return Article::leftJoin('article_trans', 'articles.id', '=', 'article_trans.article_id')
            ->where('slug', $slug)
            ->where('lang_id', $lang_id)
            ->where('active', 1)
            ->select('articles.*', 'article_trans.title', 'article_trans.description', 'article_trans.short_desc', 'article_trans.meta_title', 'article_trans.meta_description', 'article_trans.meta_keywords');
    }

    public function scopeBetweenDate($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('date_from')
                ->orWhere('date_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('date_to')
                ->orWhere('date_to', '>=', $date);
        });
    }

    public function scopeDiscount($query)
    {
        return $query->where('discount', '>', 0);
    }

}
