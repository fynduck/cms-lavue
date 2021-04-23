<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 05.02.2019
 * Time: 21:44
 */

namespace Modules\Search\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Search\Entities\SearchModule;
use Modules\Search\Jobs\SearchJob;
use Modules\Search\Transformers\SearchResponse;
use Modules\Settings\Entities\Pagination;

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

        $searchModules = Cache::remember('search_modules', now()->addDay(), function () {
            return SearchModule::pluck('models', 'name');
        });

        if (!$searchModules->count()) {
            return null;
        }

        $paginateFor = $request->get('for', 'search_page');

        $perPage = Cache::remember(
            'search_' . $paginateFor,
            now()->addDay(),
            function () use ($paginateFor) {
                return Pagination::where('on', $paginateFor)->where('for', 'items')->value('value');
            }
        );

        $perPage = $perPage ?? 10;

        $page = $request->get('page', 1);

        SearchJob::dispatch($request->get('q'));

        $results = collect();

        foreach ($searchModules as $module => $models) {
            foreach ($models as $model) {
                $modelPath = "\Modules\\$module\Entities\\$model";
                if (!class_exists($modelPath) || !method_exists($modelPath, 'scopeSearch')) {
                    continue;
                }

                try {
                    $result = $modelPath::search($request->get('q'))
                        ->lang()
                        ->active()
                        ->skip(($page - 1) * $perPage)
                        ->take($perPage + 1)
                        ->get();

                    $results = $results->merge($result);
                } catch (Exception $exception) {
                    Log::warning('SearchPage: ' . $exception->getMessage());
                }
            }
        }

        return SearchResponse::collection($results->sortByDesc('relevance'));
    }
}
