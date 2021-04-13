<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 05.02.2019
 * Time: 21:44
 */

namespace Modules\Search\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\ArticleTrans;
use Modules\Category\Entities\CategoryTrans;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;
use Modules\Product\Entities\ProductTrans;
use Modules\Search\Jobs\SearchJob;
use Modules\Search\Services\SearchService;
use Modules\Search\Transformers\SearchResponse;

class FrontController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function searchResult(Request $request): ?AnonymousResourceCollection
    {
        if (!$request->get('q')) {
            return null;
        }

        $perPage = $request->get('limit', 10);
        $page = $request->get('page', 1);

        SearchJob::dispatch($request->get('q'));

        $results = collect();

        $pageResult = PageTrans::search($request->get('q'))
            ->lang()
            ->active()
            ->skip(($page - 1) * $perPage)
            ->take($perPage + 1)
            ->get();

        $results = $results->merge($pageResult);

        $articleResult = ArticleTrans::search($request->get('q'))
            ->lang()
            ->active()
            ->skip(($page - 1) * $perPage)
            ->take($perPage + 1)
            ->get();

        $results = $results->merge($articleResult);

        return SearchResponse::collection($results->sortByDesc('relevance'));
    }
}
