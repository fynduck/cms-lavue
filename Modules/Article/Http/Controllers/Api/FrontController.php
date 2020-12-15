<?php

namespace Modules\Article\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Transformers\ArticleResource;
use Modules\Settings\Entities\Pagination;

class FrontController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getArticles(Request $request): AnonymousResourceCollection
    {
        $type = $request->get('type', Article::ARTICLES);
        $query = Article::leftJoin('article_trans as t', 'articles.id', '=', 't.article_id')
            ->select('articles.*', 't.title', 't.slug', 't.short_desc', 't.description', 't.active')
            ->where('lang_id', config('app.locale_id'))
            ->where('type', $type)
            ->where('active', 1);

        if (!$request->get('limit')) {
            $perPage = Cache::remember('articles_pagination_' . $type, 30, function () use ($type) {
                return Pagination::where('on', $type)->where('for', 'items')->value('value');
            });
        } else {
            $perPage = $request->get('limit');
        }

        if ($request->get('show_home'))
            $query->where('no_show_home', '<>', 1);

        if ($type == Article::PROMOTIONS) {
            if ($request->get('past')) {
                $query->where('date_to', '<', now())
                    ->orderBy('date_to', 'desc');
            } else {
                $query->betweenDate(now())
                    ->orderByRaw('ISNULL(date_to), date_to ASC');
            }

        } else {
            $query->orderBy('date', 'desc');
        }

        $query->orderBy('priority');

        $articles = $query->simplePaginate($perPage ?? 9);

        $additional = [
            'trans' => [
                'promo_will_last' => trans('global.promo_will_last'),
                'active_to'       => trans('global.active_to'),
                'read'            => trans('global.read'),
                'show_more'       => trans('global.show_more'),
                'further'         => trans('global.further'),
                'before_end'      => trans('global.before_end'),
                'indefinitely'    => trans('global.indefinitely'),
                'finished'        => trans('global.finished'),
                'show'            => trans('global.show'),
                'actives'         => trans('global.actives'),
                'past'            => trans('global.past'),
                'detailed'        => trans('global.detailed'),
                'days'            => trans('global.days'),
                'hours'           => trans('global.hours'),
                'minutes'         => trans('global.minutes'),
                'seconds'         => trans('global.seconds')
            ]
        ];

        return ArticleResource::collection($articles)->additional($additional);
    }

    public function article($slug)
    {
        $articleTrans = ArticleTrans::whereSlug($slug)->active()->firstOrFail();

        return new ArticleResource($articleTrans);
    }
}