<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 9/26/18
 * Time: 12:49 AM
 */

namespace Modules\Article\Services;

use Fynduck\FilesUpload\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Entities\ArticleViews;

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
            'priority'     => (int)$request->get('priority'),
            'discount'     => (float)$request->get('discount'),
            'socials'      => $request->get('socials') ? 1 : 0,
            'date'         => $request->get('date'),
            'no_show_home' => $request->get('no_show_home') ? 1 : 0,
            'date_from'    => $request->get('date_from'),
            'date_to'      => $request->get('date_to'),
        ];

        if (!empty($imagesName['imageName']))
            $data['image'] = $imagesName['imageName'];

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
            ->selectRaw('article_id AS id, title, ' . $typeName);

        if ($selected && array_key_exists($value, $selected)) {
            $current->whereIn('article_id', $selected[$value]);
            $limit += count($selected[$value]);
        } else if ($q) {
            $current->where('title', 'like', '%' . $q . '%');
        }

        return $current;
    }

    public function saveImages(Request $request): array
    {
        $nameImages = [
            'imageName' => null
        ];
        $imgName = null;
        if ($request->get('items')[config('app.fallback_locale_id')]['title'])
            $imgName = $request->get('items')[config('app.fallback_locale_id')]['title'];

        if ($request->get('image')) {
            if (!Str::contains($request->get('image'), Article::FOLDER_IMG)) {
                $sizes = null;
                $resizeMethod = null;

                $settings = Cache::remember('article_settings', now()->addDay(), function () {
                    return ArticleSettings::latest()->first();
                });
                if ($settings) {
                    $sizes = $settings->sizes;
                    $resizeMethod = $settings->resize;
                }
                $nameImages['imageName'] = UploadFile::file($request->get('image'))
                    ->setFolder(Article::FOLDER_IMG)
                    ->setName($imgName)
                    ->setOverwrite($request->get('old_image'))
                    ->setSizes($sizes)
                    ->save($resizeMethod);
            } else {
                $nameImages['imageName'] = $request->get('old_image');
            }
        }

        return $nameImages;
    }

    public function settings()
    {
        $settings = Cache::remember('article_settings', now()->addDay(), function () {
            return ArticleSettings::latest()->first();
        });

        if ($settings) {
            $sizes = [];
            foreach ($settings->sizes as $size) {
                $sizes[] = [
                    'name'   => $size['name'],
                    'width'  => $size['width'],
                    'height' => $size['height']
                ];
            }

            $settings->sizes = $sizes;
        }

        return $settings;
    }

    /**
     * Get image link by size
     * @param $image
     * @param null $size
     * @param null $key
     * @return string
     */
    public function linkImage($image, $size = null, $key = null): string
    {
        if (!$image)
            return asset('img/placeholder.jpg');

        if (!$size && !$key)
            return asset('storage/' . Article::FOLDER_IMG . '/' . $image);

        $settings = Cache::remember('article_settings', now()->addDay(), function () {
            return ArticleSettings::latest()->first();
        });

        if ($settings && $settings->sizes) {
            if ($key) {
                if ($key == 'first') {
                    return asset('storage/' . Article::FOLDER_IMG . '/' . key($settings->sizes) . '/' . $image);
                } else {
                    $keySize = 0;
                    if (count($settings->sizes) > 1) {
                        $division = is_numeric($key) ? $key : 2;
                        $keySize = round(count($settings->sizes) / $division);
                    }
                    $valueSizes = array_values($settings->sizes);
                    return asset('storage/' . Article::FOLDER_IMG . '/' . $valueSizes[$keySize]['name'] . '/' . $image);
                }
            } elseif (array_key_exists($size, $settings->sizes)) {
                return asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $image);
            }
        }

        return asset('storage/' . Article::FOLDER_IMG . '/' . $image);
    }

    /**
     * Get all links with image size
     * @param $image
     * @param bool $srcset
     * @return array
     */
    public function linkImages($image, $srcset = true): array
    {
        $images = [];

        $settings = Cache::remember('article_settings', now()->addDay(), function () {
            return ArticleSettings::latest()->first();
        });

        if ($image && $settings && $settings->sizes) {
            foreach ($settings->sizes as $size => $sizes) {
                $src = asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $image);
                if ($srcset)
                    $src .= ' ' . $sizes['width'] . 'w';

                $images[] = $src;
            }
        }

        return $images;
    }
}
