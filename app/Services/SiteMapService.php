<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 06.12.2017
 * Time: 21:55
 */

namespace App\Services;

use Carbon\Carbon;
use Modules\Article\Entities\Article;
use Modules\Category\Entities\CategoryTrans;
use Modules\Language\Entities\Language;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;
use Modules\Product\Entities\Product;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SiteMapService
{
    public function generateMap()
    {
        $lang = [];
        $languages = \Cache::remember(
            'languages',
            now()->addDay(),
            function () {
                return Language::where('active', 1)
                    ->get(['id', 'slug', 'country_iso', 'default', 'name', 'image']);
            }
        );
        foreach ($languages as $item) {
            $lang[$item->id] = $item->slug;
        }

        $pagesData = Page::leftJoin('page_trans', 'pages.id', '=', 'page_trans.page_id')
            ->whereIn('method', [Article::ARTICLES, Article::PROMOTIONS, 'catalog', 'home'])
            ->get();

        $pageUrls = [];
        foreach ($pagesData as &$page) {
            $pageUrls[$page->method][$page->lang] = $page->url;
        }

        $domainLang = [];
        foreach ($pageUrls['home'] as $langId => $page) {
            if (isset($lang[$langId])) {
                $domainLang[] = $lang[$langId];
            }
        }

        $siteMap = Sitemap::create()->add(Url::create(config('app.url'))->setPriority(1.0));
        foreach ($domainLang as $item) {
            $siteMap->add(
                Url::create(config('app.url') . '/' . $item)
                    ->setPriority(1.0)
            );
        }

        PageTrans::where('status', 1)->where('url', '!=', '')->get()->each(
            function (PageTrans $page) use ($siteMap, $lang) {
                if (count($lang) > 1) {
                    $addUrl = Url::create(url($lang[$page->lang], $page->url));
                } else {
                    $addUrl = Url::create(url($page->url));
                }

                $siteMap->add(
                    $addUrl
                        ->setLastModificationDate(Carbon::yesterday())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                        ->setPriority(0.85)
                );
            }
        );

        Article::leftJoin('article_trans', 'articles.id', '=', 'article_trans.article_id')
            ->where('active', 1)
            ->get()->each(
                function (Article $newsItem) use ($siteMap, $lang, $pageUrls) {
                    if (count($lang) > 1) {
                        $addUrl = Url::create(
                            url($lang[$newsItem->lang], [$pageUrls[$newsItem->type][$newsItem->lang], $newsItem->slug])
                        );
                    } else {
                        $addUrl = Url::create(url($pageUrls[$newsItem->type][$newsItem->lang], $newsItem->slug));
                    }

                    $siteMap->add(
                        $addUrl
                            ->setLastModificationDate(Carbon::yesterday())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                            ->setPriority(0.85)
                    );
                }
            );

        CategoryTrans::where('active', 1)->where('slug', '!=', '')->get()->each(
            function (CategoryTrans $categoriesTrans) use ($siteMap, $lang, $pageUrls) {
                if (count($lang) > 1) {
                    $addUrl = Url::create(
                        url(
                            $lang[$categoriesTrans->lang_id],
                            [$pageUrls['catalog'][$categoriesTrans->lang_id], $categoriesTrans->slug]
                        )
                    );
                } else {
                    $addUrl = Url::create(url($pageUrls['catalog'][$categoriesTrans->lang_id], $categoriesTrans->slug));
                }

                $siteMap->add(
                    $addUrl
                        ->setLastModificationDate(Carbon::yesterday())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                        ->setPriority(0.85)
                );
            }
        );

        $categories = CategoryTrans::where('active', 1)->where('slug', '!=', '')->get();
        $categoriesUrl = [];
        foreach ($categories as $item) {
            $categoriesUrl[$item->lang_id][$item->category_id] = $item->slug;
        }

        Product::leftJoin('product_trans', 'products.id', '=', 'product_trans.product_id')
            ->where('active', 1)->where('slug', '!=', '')->get()->each(
                function (Product $products) use ($siteMap, $lang, $pageUrls, $categoriesUrl) {
                    if (count($lang) > 1) {
                        $addUrl = Url::create(
                            url(
                                $lang[$products->lang_id],
                                [
                                    $pageUrls['catalog'][$products->lang_id],
                                    $categoriesUrl[$products->lang_id][$products->category_id],
                                    $products->slug
                                ]
                            )
                        );
                    } else {
                        $addUrl = Url::create(
                            url(
                                $pageUrls['catalog'][$products->lang_id],
                                $categoriesUrl[$products->lang_id][$products->category_id],
                                $products->slug
                            )
                        );
                    }

                    $siteMap->add(
                        $addUrl
                            ->setLastModificationDate(Carbon::yesterday())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                            ->setPriority(0.69)
                    );
                }
            );

        $siteMap->writeToFile(public_path('sitemap.xml'));
    }
}
