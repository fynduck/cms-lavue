<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SetGlobal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Article\Entities\Article;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;
use Modules\Settings\Entities\Pagination;

class FrontController extends Controller
{
    /**
     * @param string $page
     * @param null $category
     * @param string $url
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param null $param
     */
    protected function pages($page = '', $category = null, $url = null)
    {
        /**
         * Get info page
         */
        if ($page) {
            $this->data['page'] = Page::getPageBySlug($page);

            /**
             * 404
             */
            if (!$this->data['page']) {
                return (new SetGlobal())->page404($this->data, [$page, $category, $url]);
            }

            if ($this->data['page']->parent_id) {
                $parent_page = Page::parent($this->data['page']->parent_id);
                if ($parent_page) {
                    SetGlobal::setBreadcrumbs($this->data, route('pages', $parent_page->url), $parent_page->title);
                }
            }

            if (is_null($category)) {
                SetGlobal::setBreadcrumbs($this->data, '', $this->data['page']->title);
            } else {
                SetGlobal::setBreadcrumbs($this->data, route('pages', $this->data['page']->url), $this->data['page']->title);
            }
        } else {
            if (!$this->data['page']) {
                $this->data['page'] = Page::getDefault('home');
            }
        }

        /**
         * 404
         */
        if (!$this->data['page']) {
            return (new SetGlobal())->page404($this->data, [$page, $category, $url]);
        }

        // set meta data for page
        SetGlobal::setMeta($this->data, $this->data['page']);
        $urlsPage = PageTrans::getSlugs($this->data['page']->page_id);
        SetGlobal::setLanguagesMenu($this->data, $urlsPage);

        if ($this->data['page']->method && $this->data['page']->module == null && method_exists(
                $this,
                $this->data['page']->method
            )) {
            //if static page
            if (!is_null($url)) {
                return $this->{$this->data['page']->method}($this->request, $category, $url);
            } elseif (!is_null($category)) {
                return $this->{$this->data['page']->method}($this->request, $category);
            } else {
                return $this->{$this->data['page']->method}($this->request);
            }
        } elseif ($this->data['page']->module) {
            $this->data['request'] = $this->request;

            return app('Modules\\' . $this->data['page']->module . '\Http\Controllers\FrontController')->index(
                $this->data,
                $this->data['page']->method,
                $category,
                $url
            );
        } else {
            if ($this->data['page']->sql_products) {
                $this->data['request'] = $this->request;

                return $this->custom($this->data);
            }

            return $this->index($this->data, $this->data['page']->method);
        }
    }

    /**
     * Show the home page
     *
     * @return View
     */
    protected function home()
    {
        unset($this->data['breadcrumbs']);

        return view('home', $this->data);
    }

    public function index($data, $method)
    {
        if ($method) {
            $urlsPage = PageTrans::where('page_id', $data['page']->page_id)->where('active', 1)->pluck('slug', 'lang_id');
            SetGlobal::setLanguagesMenu($data, $urlsPage, false);

            SetGlobal::setMeta($data, $data['page']);
            SetGlobal::setBanners($data);

            return $this->{$method}($data);
        }

        return view('page::show_page', $data);
    }

    protected function contacts($data)
    {
        $company = null;
        $data['address'] = isset($data['settings']['contact_address']) ? $data['settings']['contact_address'] : '';

        return view('page::contacts', $data);
    }

    protected function about($data)
    {
        return view('page::about', $data);
    }

    protected function hits($data)
    {
        return view('page::show_page_hits_promo', $data);
    }

    protected function sales($data)
    {
        return view('page::show_page_hits_promo', $data);
    }
}
