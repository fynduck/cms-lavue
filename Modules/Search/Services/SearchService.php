<?php

namespace Modules\Search\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Article\Entities\ArticleTrans;
use Modules\Category\Entities\CategoryTrans;
use Modules\Product\Entities\ProductTrans;
use Modules\Search\Entities\SearchModule;

class SearchService
{
    protected $exceptModules = [
        'User',
        'UserGroup',
        'Translate',
        'Settings',
        'Search',
        'Redirect',
        'Module',
        'Language',
        'Menu',
        'CustomForm',
    ];

    public function listModules($activeModules): array
    {
        $statuses = json_decode(file_get_contents(base_path('modules_statuses.json')), true);

        $searchModule = SearchModule::pluck('models', 'name')->toArray();
        $modules = [];

        foreach ($activeModules as $moduleName => $path) {
            if (!in_array($moduleName, $this->exceptModules) &&
                array_key_exists($moduleName, $statuses) &&
                $statuses[$moduleName]) {
                $models = [];
                foreach (File::files($path . '/Entities') as $model) {
                    $extension = strlen($model->getExtension());
                    $modelName = substr($model->getFilename(), 0, -($extension + 1));
                    $models[] = [
                        'name'   => $modelName,
                        'active' => $this->checkSearchModelExist($searchModule, $moduleName, $modelName)
                    ];
                }

                $modules[] = [
                    'name'   => $moduleName,
                    'models' => $models
                ];
            }
        }

        return $modules;
    }

    protected function checkSearchModelExist(array $searchModule, string $module, string $model): bool
    {
        return array_key_exists($module, $searchModule) && array_search($model, $searchModule[$module]) !== false;
    }
}