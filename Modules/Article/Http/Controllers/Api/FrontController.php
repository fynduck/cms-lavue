<?php

namespace Modules\Article\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Transformers\ArticleResource;
use Modules\Page\Entities\Page;
use Modules\Page\Services\PageService;
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

        return ArticleResource::collection($articles);
    }

    public function article($slug)
    {
        $articleTrans = ArticleTrans::whereSlug($slug)->active()->first();

        if (!$articleTrans) {
            return PageService::notFound();
        }

        return new ArticleResource($articleTrans);
    }
}