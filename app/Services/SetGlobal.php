<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 23.03.2017
 * Time: 8:41
 */

namespace App\Services;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Modules\Banner\Entities\Banner;
use Modules\Page\Entities\Page;
use Modules\Redirect\Services\RedirectService;
use Modules\Statistics\Services\StatisticService;

class SetGlobal
{
    public static function generateRoute($data, $items)
    {
        foreach ($items as $item) {
            $item->link = generateRoute($data['urlsPages'], $item);
        }

        return $items;
    }

    /**
     * Set breadcrumbs
     * @param $data
     * @param $url
     * @param $title
     * @param bool $setTitlePage
     * @return mixed
     */
    public static function setBreadcrumbs(&$data, $url, $title, $setTitlePage = true)
    {
        $data['breadcrumbs'][] = ['url' => $url, 'title' => $title];
        if ($setTitlePage) {
            $data['current_title_page'] = $title;
        }
    }

    /**
     * set banners for page.
     *
     * @param $data
     * @param bool|null $page_id
     * @param bool $page_type
     * @return array
     */
    public static function setBanners(&$data, $page_id = false, $page_type = false)
    {
        if (!$page_id) {
            $page_id = $data['page']->page_id;
            $page_type = 'page';
        }

        if ($page_id && checkModule('Banner')) {
            $banners = Banner::getByPageId($page_id, $page_type)
                ->where(
                    function ($from) {
                        $from->whereNull('date_from')
                            ->orWhere('date_from', '<=', now()->toDateString());
                    }
                )
                ->where(
                    function ($to) {
                        $to->whereNull('date_to')
                            ->orWhere('date_to', '>=', now()->toDateString());
                    }
                )
                ->get();

            $banners = self::generateRoute($data, $banners);
            $banners = Arrays::setTwoKeys($banners, 'type', 'id');
            if (isset($banners['top'])) {
                $data['bannersTop'] = $banners['top'];
            }
            if (isset($banners['content'])) {
                $data['bannersContent'] = $banners['content'];
            }
            if (isset($banners['left'])) {
                $data['bannersLeft'] = $banners['left'];
            }
            if (isset($banners['right'])) {
                $data['bannersRight'] = $banners['right'];
            }
        }
    }

    /**
     * Set meta
     * @param $data
     * @param $page
     * @return array
     * @internal param $data
     */
    public static function setMeta(&$data, $page)
    {
        $data['meta_title'] = '';
        $data['meta_description'] = '';
        $data['meta_keywords'] = '';

        /**
         * Set meta title
         */
        if (isset($page->meta_title) && $page->meta_title) {
            $data['meta_title'] = $page->meta_title;
        } elseif (isset($page->title) && $page->title) {
            $data['meta_title'] = $page->title;
        }

        /**
         * Set meta description
         */
        if (isset($page->meta_description) && $page->meta_description) {
            $data['meta_description'] = $page->meta_description;
        } elseif (isset($page->description) && strip_tags($page->description)) {
            $data['meta_description'] = Str::limit(html_entity_decode(strip_tags($page->description)), 140, '');
        }

        /**
         * Set meta keywords
         */
        if (isset($page->meta_keywords) && $page->meta_keywords) {
            $data['meta_keywords'] = $page->meta_keywords;
        } elseif (isset($page->description) && strip_tags($page->description)) {
            $data['meta_keywords'] = Str::limit(strip_tags($page->description), 140, '');
        }

        return $data;
    }

    /**
     * set languages menu
     * @param $data
     * @param $urls
     * @return mixed
     */
    public static function setLanguagesMenu(&$data, $urls)
    {
        $data['more_lang'] = false;
        $data['languagesMenu'] = [];
        foreach (config('app.locales') as $locale) {
            if (isset($urls[$locale->id]) && $data['page']->lang != $locale->id) {
                $data['languagesMenu'][$locale->id]['name'] = $locale->name;
                $linkParams[] = $locale->slug;
                if ($urls[$locale->id]) {
                    $linkParams[] = $urls[$locale->id];
                }

                $data['languagesMenu'][$locale->id]['link'] = url(implode('/', $linkParams));
            }
        }

        $data['more_lang'] = count($data['languagesMenu']);

        return $data;
    }

    public function page404($data, $params)
    {
        $slugs = $params;
        $langPrefix = config('app.locale_prefix');
        array_unshift($slugs, $langPrefix);
        $slugs = array_filter($slugs);

        if ($slugs && checkModule('Redirect')) {
            $responseRedirect = (new RedirectService())->checkHasRedirect(implode('/', $slugs), $params, $langPrefix);
            if ($responseRedirect) {
                return redirect($responseRedirect['redirectTo'], $responseRedirect['status']);
            }
        }

        /**
         * Not found redirect
         */
        $data['page'] = Page::getDefault('not_found');
        if (!is_null($data['page'])) {
            self::setMeta($data, $data['page']);
            unset($data['breadcrumbs']);
            $data['breadcrumbs'][] = ['url' => route('home'), 'title' => trans('global.home')];
            SetGlobal::setBreadcrumbs($data, '', $data['page']->title, false);
            $data['current_title_page'] = 404;
            self::setBanners($data);

            return Response::view('page::show_page', $data, 404);
        }

        return abort(404);
    }

    /**Set views
     * @param $request
     * @param string $nameCookie
     * @param int $id
     * @param string $type
     * @param bool $ctr
     * @return void
     */
    public function setViews($request, string $nameCookie, int $id, string $type, bool $ctr = true)
    {
        $view = $request->cookie($nameCookie . $id);
        if (!$view && checkModule('Statistics')) {
            (new StatisticService())->log($request, $type, [$id], $ctr);
        }
    }
}
