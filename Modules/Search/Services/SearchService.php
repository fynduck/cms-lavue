<?php

namespace Modules\Search\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Article\Entities\ArticleTrans;
use Modules\Category\Entities\CategoryTrans;
use Modules\Product\Entities\ProductTrans;

class SearchService
{
    public function searchOnCategories(Request $request, &$result, $urlsPages, $limit = 10)
    {
        if (checkModule('Category')) {
            $categories = CategoryTrans::where('active', 1)
                ->where('lang_id', config('app.locale_id'))
                ->search($request->get('q'))
                ->simplePaginate($limit);

            foreach ($categories as $key => $category) {
                $result['categories'][$key]['title'] = $category->title;
                $description = strip_tags($category->short_desc) ? strip_tags($category->short_desc) : strip_tags($category->description);
                $result['categories'][$key]['description'] = Str::limit($description, 160);
                $result['categories'][$key]['link'] = route('pages', [$urlsPages['catalog'], $category->slug]);
            }
        }
    }

    public function searchOnProducts(Request $request, &$result, $urlsPages, $limit = 10)
    {
        if (checkModule('Product')) {
            $products = ProductTrans::where('active', 1)
                ->where('lang_id', config('app.locale_id'))
                ->search($request->get('q'))
                ->simplePaginate($limit);

            foreach ($products as $key => $product) {
                $result['products'][$key]['title'] = $product->title;
                $result['products'][$key]['description'] = Str::limit(strip_tags($product->description), 160);
                $result['products'][$key]['link'] = route('pages', [$urlsPages['catalog'], $product->product->getCategoryTrans()->lang()->value('slug'), $product->slug]);
            }
        }
    }

    public function searchOnArticles(Request $request, &$result, $urlsPages, $limit = 10, $type = null)
    {
        if (checkModule('Article')) {
            $query = ArticleTrans::where('active', 1)
                ->where('lang_id', config('app.locale_id'));

            if ($type) {
                $query->whereHas('getArticle', function ($query) use ($type) {
                    $query->where('type', $type);
                });
            }

            $articles = $query->search($request->get('q'))
                ->simplePaginate($limit);

            foreach ($articles as $key => $article) {
                $type = $article->getArticle->type;
                $result[$type][$key]['title'] = $article->title;
                $description = strip_tags($article->short_desc) ? strip_tags($article->short_desc) : strip_tags($article->description);
                $result[$type][$key]['description'] = Str::limit($description, 160);
                $result[$type][$key]['link'] = null;
                if ($type)
                    $result[$type][$key]['link'] = route('pages', [$urlsPages[$type], $article->slug]);
            }
        }
    }
}
