<?php

namespace Modules\Page\Traits;

use Illuminate\Http\Request;
use Modules\Page\Entities\Page;

trait PageTrait
{
    /**
     * @param $query
     * @param Request $request
     */
    public function scopeFilter($query, Request $request)
    {
        if ($request->get('q')) {
            $query->where('title', 'LIKE', '%' . $request->get('q') . '%')
                ->orWhere('description', 'LIKE', '%' . $request->get('q') . '%');
        }

        if ($request->get('active')) {
            $query->where('active', $request->get('active'));
        }

        if ($request->get('lang_id')) {
            $query->where('lang_id', $request->get('lang_id'));
        }
    }

    /**
     * Select default page (home page)
     * @param string $method
     * @param null $lang_id
     * @return mixed
     */
    static function getDefault($method = 'home', $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');

        return Page::leftJoin('page_trans', 'pages.id', '=', 'page_trans.page_id')
            ->where('lang_id', $lang_id)
            ->where('method', $method)
            ->where('active', 1)
            ->first();
    }

    /**
     * Select item by id
     * @param $id
     * @param null $active
     * @param null $lang_id
     * @return mixed
     */
    static function getPageById($id, $active = null, $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');

        $query = Page::leftJoin('page_trans', 'pages.id', '=', 'page_trans.page_id')
            ->where('lang_id', $lang_id)
            ->where('pages.id', $id);
        if ($active) {
            $query->where('active', $active);
        }

        return $query->first();
    }

    /**
     * Select item by slug
     * @param $slug
     * @param null $lang_id
     * @return mixed
     */
    static function getPageBySlug($slug, $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');

        return Page::leftJoin('page_trans', 'pages.id', '=', 'page_trans.page_id')
            ->where('slug', $slug)
            ->where('lang_id', $lang_id)
            ->where('active', 1)
            ->first();
    }

    /**
     * Select slug for all static pages
     * @param null $lang_id
     * @return \Illuminate\Support\Collection
     * @internal param $method (string or array)
     */
    static function getSlugAllStaticPages($lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');

        return Page::leftJoin('page_trans', 'pages.id', '=', 'page_trans.page_id')
            ->where('lang_id', $lang_id)
            ->where('active', 1)
            ->where('method', '<>', '')
            ->pluck('slug', 'method');
    }

    static function getPageByMethod($method, $active = 1, $lang_id = null)
    {
        $lang_id = $lang_id ?? config('app.locale_id');

        $query = Page::leftJoin('page_trans', 'pages.id', '=', 'page_trans.page_id')
            ->where('lang_id', $lang_id);

        if ($active) {
            $query->where('active', 1);
        }
        if (is_array($method)) {
            $query->whereIn('method', $method);
        } else {
            $query->where('method', $method);
        }

        return $query->firstOrFail();
    }
}