<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Article\Services\ArticleService;
use Modules\Page\Services\PageService;
use Nwidart\Modules\Facades\Module;

class AdminSearch extends AdminController
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function liveSelect(Request $request)
    {
        $searchIn = $this->searchIn();

        $q = trim($request->get('q'));
        $single = $request->get('single');

        //set search in
        if ($single) {
            if (isset($searchIn[$single])) {
                $aux = $searchIn[$single];
                unset($searchIn);
                $searchIn[$single] = $aux;
            } else
                unset($searchIn);

        } elseif ($request->get('searchIn')) {
            $search_in = explode(',', $request->get('searchIn'));
            foreach ($searchIn as $key => $value) {
                if (!in_array($key, $search_in))
                    unset($searchIn[$key]);
            }
        }

        //set selected values
        $selected = $request->get('selected');
        $data['selected'] = [];
        if ($selected) {
            if (is_array($selected)) {
                foreach ($selected as $item) {
                    $item = explode('_', $item);
                    if (array_key_exists(1, $item))
                        $data['selected'][$item[0]][] = $item[1];
                }
            } else {
                $item = explode('_', $selected);
                if (array_key_exists(1, $item))
                    $data['selected'][$item[0]][] = $item[1];
            }
        }
        if (!empty($searchIn) && ($q || $data['selected'])) {
            $current = $query = false;

            foreach ($searchIn as $type => $value) {
                if (!$q && !isset($data['selected'][$value]))
                    continue;
                $typeName = \DB::raw("'$value' AS type");
                if ($q)
                    $limit = 5;
                else
                    $limit = 0;
                switch ($type) {
                    case 'pages':
                        if (checkModule('Page'))
                            $current = (new PageService())->searchPage($limit, $typeName, $value, $q, $data['selected']);
                        break;
                    case 'articles':
                    case 'news':
                    case 'promotions':
                        if (checkModule('Article'))
                            $current = (new ArticleService())->searchArticles($limit, $typeName, $value, $q, $data['selected']);
                        break;
                    default:
                        break;
                }
                if ($current) {
                    $current->limit($limit);

                    if ($query)
                        $query->union($current);
                    else
                        $query = clone $current;
                }

            }
            //final
            if ($query) {
                $data['items'] = $query->orderBy('type')->orderBy('title')->get();

                return response()->json($this->parseObjectShow($data));
            }
        }

        return response()->json([]);
    }

    private function searchIn(): array
    {
        $modules = [];
        foreach (Module::allEnabled() as $moduleName => $module)
            $modules[mb_strtolower(Str::plural($moduleName))] = mb_strtolower($moduleName);

        return $modules;
    }

    /**
     * Parse object for live search
     * @param $data
     * @return array
     */
    private function parseObjectShow($data)
    {
        $types = $this->typesObject();

        $items = $data['items']->sortBy('type');
        $pages = [];
        foreach ($items as $item) {
            $pages[] = [
                'label'       => $item->title,
                'module_name' => $types[$item->type],
                'value'       => $item->type . '_' . $item->id
            ];
        }

        return $pages;
    }

    /**
     * Types for group object items by type
     * @return array
     */
    private function typesObject()
    {
        return [
            'page'         => trans('admin.pages'),
            'manufacturer' => trans('admin.manufacturers'),
            'video'        => trans('admin.videos'),
            'article'      => trans('admin.articles'),
            'news'         => trans('admin.news'),
            'promotion'    => trans('admin.promotions'),
            'category'     => trans('admin.categories'),
            'product'      => trans('admin.products')
        ];
    }
}
