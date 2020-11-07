<?php

namespace Modules\Article\Http\Controllers;

use App\Services\SetGlobal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Transformers\ArticleResource;
use Modules\Settings\Entities\Pagination;

class FrontController extends Controller
{
    /**
     * @param $data
     * @param $method
     * @param $segment2
     * @param $segment3
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|Response|\Illuminate\View\View
     */
    public function index($data, $method, $segment2, $segment3)
    {
        setlocale(LC_ALL, config('app.faker_locale') . '.utf8');

        if (!$segment2)
            return $this->articles($data, $method, $segment2);

        return $this->article($data, $method, $segment2);
    }

    /**
     * List articles
     * @param $data
     * @param $method
     * @param $categoryArticle
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    protected function articles($data, $method, $categoryArticle)
    {
        return view('article::show_articles', $data);
    }

    /**
     * Page detail article
     * @param $data
     * @param $method
     * @param $url
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    protected function article($data, $method, $url)
    {
        $data['item'] = Article::getItemBySlug($url)
            ->where('type', $method)
            ->first();

        if ($data['item']) {
            $data['seo_images'] = 'images/articles/md/' . $data['item']->image;
            SetGlobal::setBanners($data, $data['item']->id, $data['page']->method);

            SetGlobal::setBreadcrumbs($data, '', $data['item']->title, false);

            SetGlobal::setMeta($data, $data['item']);

            //language menu
            $urlsPage = ArticleTrans::getSlugs($data['item']->id);
            SetGlobal::setLanguagesMenu($data, $urlsPage, false);

            $data['labels'] = json_encode([
                'days'    => trans('global.days'),
                'hours'   => trans('global.hours'),
                'minutes' => trans('global.minutes'),
                'seconds' => trans('global.seconds')
            ]);

            if ($method != 'articles')
                return view('article::show_news', $data);

            $perPage = Cache::remember('relate_article_per_page', now()->addDay(), function () {
                return Pagination::where('on', 'articles')->where('for', 'relate_articles')->value('value');
            });
            $data['relate_articles'] = Article::getAll('articles', 1)
                ->where('article_id', '<>', $data['item']->id)
                ->limit($perPage ?? 6)->get();

            return view('article::show_article', $data);
        } else {

            return (new SetGlobal())->page404($data, [$data['page']->url, $url]);
        }
    }

    /**
     * Get list articles
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
}
