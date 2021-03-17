<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 11.05.2017
 * Time: 19:34
 */

namespace Modules\Page\Services;

use Illuminate\Http\Request;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;
use Modules\Page\Transformers\PageClientResource;

class PageService
{
    /**
     * @param Request $request
     * @param int $id
     * @return Page
     */
    public static function addUpdate(Request $request, int $id = 0): Page
    {
        $page = Page::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'sql_products' => $request->get('sql_products') ? $request->get('sql_products') : '',
                'socials'      => $request->get('socials'),
            ]
        );

        if (!$page) {
            return back()->withErrors(trans('admin.data_not_save'));
        }

        return $page;
    }

    /**
     * @param $id
     * @param $items
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function addUpdateTrans(int $id, array $items)
    {
        foreach ($items as $lang => $item) {
            $pageLang = PageTrans::updateOrCreate(
                [
                    'page_id' => $id,
                    'lang_id' => $lang
                ],
                [
                    'page_id'            => $id,
                    'title'              => $item['title'],
                    'description'        => $item['description'],
                    'description_footer' => !empty($item['description_footer']) ? $item['description_footer'] : '',
                    'slug'               => !empty($item['slug']) ? $item['slug'] : '',
                    'meta_title'         => $item['meta_title'],
                    'meta_description'   => $item['meta_description'],
                    'meta_keywords'      => $item['meta_keywords'],
                    'active'             => $item['active'],
                ]
            );

            if (!$pageLang) {
                return back()->withErrors(trans('admin.data_not_save'));
            }
        }
    }

    public function searchPage(&$limit, $typeName, $value, $q, $selected)
    {
        $current = PageTrans::selectRaw('page_id AS id, title, ' . $typeName);

        if ($selected && array_key_exists($value, $selected)) {
            $current->where('lang_id', config('app.locale_id'))
                ->whereIn('page_id', $selected[$value]);
            $limit += count($selected[$value]);
        } elseif ($q) {
            $current->where('title', 'like', '%' . $q . '%');
        }

        return $current;
    }

    public static function notFound(): PageClientResource
    {
        $page = Page::getPageByMethod('not_found');

        $pageLang = PageTrans::where('page_id', $page->page_id)
            ->where('active', 1)
            ->pluck('lang_id')
            ->toArray();

        return (new PageClientResource($page))->additional(['page_lang' => $pageLang]);
    }
}
