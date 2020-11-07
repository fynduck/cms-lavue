<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 9/26/18
 * Time: 12:49 AM
 */

namespace Modules\Article\Services;

use Fynduck\FilesUpload\PrepareFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Entities\ArticleViews;
use Modules\Statistics\Entities\Ip;

class ArticleService
{
    public static function parseArticle(&$item, string $page = '', string $size = 'xs')
    {
        if (!$page)
            $page = cache('urls_pages_' . config('app.locale_id'))[$item->type];

        $item->img = 'https://via.placeholder.com/480/250';
        if ($item->image)
            $item->img = asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $item->image);

        $item->link = route('pages', [$page, $item->url]);
    }

    /**
     * @param Request $request
     * @param array $imagesName
     * @param int $id
     * @return mixed
     */
    public function addUpdate(Request $request, array $imagesName = [], int $id = null)
    {
        $data = [
            'type'         => $request->get('type'),
            'sort'         => (int)$request->get('sort'),
            'discount'     => (float)$request->get('discount'),
            'socials'      => $request->get('socials') ? 1 : 0,
            'date'         => $request->get('date'),
            'no_show_home' => $request->get('no_show_home') ? 1 : 0,
            'date_from'    => $request->get('date_from'),
            'date_to'      => $request->get('date_to'),
        ];

        if (!empty($imagesName['imageName']))
            $data['image'] = $imagesName['imageName'];
        if (!empty($imagesName['iconName']))
            $data['icon'] = $imagesName['iconName'];

        return Article::updateOrCreate(
            [
                'id' => $id
            ],
            $data);
    }

    public function addUpdateTrans(int $id, array $items)
    {
        foreach ($items as $lang_id => $item) {
            $itemLang = ArticleTrans::updateOrCreate([
                'article_id' => $id,
                'lang_id'    => $lang_id
            ], [
                    'title'            => $item['title'],
                    'slug'             => $item['slug'],
                    'description'      => $item['description'],
                    'short_desc'       => Str::limit(strip_tags($item['short_desc']), 255, ''),
                    'active'           => $item['active'],
                    'meta_title'       => $item['meta_title'],
                    'meta_description' => $item['meta_description'],
                    'meta_keywords'    => $item['meta_keywords']
                ]
            );

            if (!$itemLang)
                return back()->withErrors(trans('admin.data_not_save'));
        }
    }

    public function checkView(Request $request, string $type, int $id)
    {
        //        $oldView = AnalyticsViews::where('session_id', $event->session_id)->value('id');
        $ip = Ip::firstOrCreate(['ip' => $request->ip()]);
        $view = ArticleViews::firstOrCreate([
            'session_id' => session()->getId(),
            'type'       => $type,
            'article_id' => $id,
        ],
            [
                'ip_id'      => $ip->id,
                'user_agent' => $request->header('user-agent'),
                'views'      => 0,
            ]
        );
        //        if (!$oldView)
        $view->increment('views');
    }

    public function searchArticles(&$limit, $typeName, $value, $q, $selected)
    {
        $current = Article::leftJoin('article_trans', 'articles.id', '=', 'article_trans.article_id')
            ->where('lang_id', config('app.locale_id'))
//            ->where('type', $type)
            ->selectRaw('article_id AS id, title, ' . $typeName);

        if ($selected && array_key_exists($value, $selected)) {
            $current->whereIn('article_id', $selected[$value]);
            $limit += count($selected[$value]);
        } else if ($q) {
            $current->where('title', 'like', '%' . $q . '%');
        }

        return $current;
    }

    public function syncTags(Request $request, Article $article)
    {
        $tags = [];
        foreach (explode(',', $request->get('tag_ids')) as $tag) {
            if ($tag)
                $tags[$tag] = ['type' => 'articles'];
        }

        $article->getTags()->sync($tags);
    }

    public function saveImages(Request $request)
    {
        $nameImages = [
            'imageName' => null,
            'iconName'  => null
        ];
        $imgName = null;
        if ($request->get('items')[config('app.fallback_locale_id')]['title'])
            $imgName = $request->get('items')[config('app.fallback_locale_id')]['title'];

        if ($request->get('image')) {
            if (!\Str::contains($request->get('image'), Article::FOLDER_IMG))
                $nameImages['imageName'] = PrepareFile::uploadBase64(Article::FOLDER_IMG, 'image', $request->get('image'), $imgName, $request->get('old_image'), Article::getSizes());
            else
                $nameImages['imageName'] = $request->get('old_image');
        }
        if ($request->get('icon')) {
            if (!\Str::contains($request->get('icon'), Article::FOLDER_IMG))
                $nameImages['iconName'] = PrepareFile::uploadBase64(Article::FOLDER_IMG, 'image', $request->get('icon'), $imgName, $request->get('old_icon'), Article::getSizes());
            else
                $nameImages['iconName'] = $request->get('old_icon');
        }

        return $nameImages;
    }
}
