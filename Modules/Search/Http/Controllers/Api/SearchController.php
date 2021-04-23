<?php

namespace Modules\Search\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Search\Entities\SearchModule;
use Modules\Search\Services\SearchService;
use Nwidart\Modules\Facades\Module;

class SearchController extends AdminController
{
    protected $modules = [];

    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->middleware('admin');

        $this->searchService = $searchService;

        foreach (Module::getCached() as $module) {
            $this->modules[$module['name']] = $module['path'];
        }
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $modules = $this->searchService->listModules($this->modules);

        return response()->json($modules);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return bool
     */
    public function store(Request $request): bool
    {
        SearchModule::truncate();

        foreach ($request->all() as $module) {
            if (!array_key_exists($module['name'], $this->modules)) {
                continue;
            }

            $models = [];
            foreach ($module['models'] as $model) {
                if ($model['active']) {
                    $models[] = $model['name'];
                }
            }

            if ($models) {
                SearchModule::create(
                    [
                        'name'   => $module['name'],
                        'models' => $models
                    ]
                );
            }
        }

        Cache::forget('search_modules');

        return true;
    }
}
