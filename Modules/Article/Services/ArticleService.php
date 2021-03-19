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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;
use Modules\Article\Entities\ArticleTrans;

class ArticleService
{
    public static function parseArticle(&$item, string $page = '', string $size = 'xs')
    {
        if (!$page) {
            $page = cache('urls_pages_' . config('app.locale_id'))[$item->type];
        }

        $item->img = 'https://via.placeholder.com/480/250';
        if ($item->image) {
            $item->img = asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $item->image);
        }

        $item->link = route('pages', [$page, $item->url]);
    }

    /**
     * @param Request $request
     * @param array $imagesName
     * @param int|null $id
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

        if (!empty($imagesName['imageName'])) {
            $data['image'] = $imagesName['imageName'];
        }

        return Article::updateOrCreate(
            [
                'id' => $id
            ],
            $data
        );
    }

    public function addUpdateTrans(int $id, array $items)
    {
        foreach ($items as $lang_id => $item) {
            $itemLang = ArticleTrans::updateOrCreate(
                [
                    'article_id' => $id,
                    'lang_id'    => $lang_id
                ],
                [
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

            if (!$itemLang) {
                return back()->withErrors(trans('admin.data_not_save'));
            }
        }
    }

    public function searchArticles(&$limit, $typeName, $value, $q, $selected)
    {
        $current = ArticleTrans::selectRaw('article_id AS id, title, ' . $typeName);

        if ($selected && array_key_exists($value, $selected)) {
            $current->where('lang_id', config('app.locale_id'))
                ->whereIn('article_id', $selected[$value]);
            $limit += count($selected[$value]);
        } elseif ($q) {
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
        if ($request->get('items')[config('app.fallback_locale_id')]['title']) {
            $imgName = $request->get('items')[config('app.fallback_locale_id')]['title'];
        }

        if ($request->get('image')) {
            if (!Str::contains($request->get('image'), Article::FOLDER_IMG)) {
                $sizes = null;
                $resizeMethod = null;
                $greyscale = false;
                $blur = 1;
                $brightness = 0;
                $background = null;
                $optimize = false;

                $settings = Cache::remember(
                    'article_sizes',
                    now()->addDay(),
                    function () {
                        return ArticleSettings::where('name', 'sizes')->first();
                    }
                );
                if ($settings && !empty($settings->data['sizes'])) {
                    $sizes = $settings->data['sizes'];
                    $resizeMethod = $settings->data['action'];
                    $greyscale = !empty($settings->data['greyscale']) ? $settings->data['greyscale'] : $greyscale;
                    $blur = !empty($settings->data['blur']) ? $settings->data['blur'] : $blur;
                    $brightness = !empty($settings->data['brightness']) ? $settings->data['brightness'] : $brightness;
                    $background = !empty($settings->data['background']) ? $settings->data['background'] : $background;
                    $optimize = !empty($settings->data['optimize']) ? $settings->data['optimize'] : $optimize;
                }
                $nameImages['imageName'] = UploadFile::file($request->get('image'))
                    ->setFolder(Article::FOLDER_IMG)
                    ->setName($imgName)
                    ->setOverwrite($request->get('old_image'))
                    ->setSizes($sizes)
                    ->setGreyscale($greyscale)
                    ->setBlur($blur)
                    ->setBrightness($brightness)
                    ->setBackground($background)
                    ->setOptimize($optimize)
                    ->save($resizeMethod);
            } else {
                $nameImages['imageName'] = $request->get('old_image');
            }
        }

        return $nameImages;
    }

    public function settings()
    {
        $settings = Cache::remember(
            'article_sizes',
            now()->addDay(),
            function () {
                return ArticleSettings::where('name', 'sizes')->first();
            }
        );

        $data = [];
        if ($settings && !empty($settings->data['sizes'])) {
            $data = $settings->data;
            $sizes = [];
            foreach ($settings->data['sizes'] as $size) {
                $sizes[] = [
                    'name'   => $size['name'],
                    'width'  => $size['width'],
                    'height' => $size['height']
                ];
            }

            $data['sizes'] = $sizes;

            foreach ($this->defaultSettings() as $key => $defaultSetting) {
                if (!array_key_exists($key, $data)) {
                    $data[$key] = $defaultSetting;
                }
            }
        }

        return $data;
    }

    public function defaultSettings()
    {
        return [
            'ratios'     => [
                'width'  => 0,
                'height' => 0,
            ],
            'ratio'      => false,
            'action'     => 'resize-crop',
            'optimize'   => null,
            'greyscale'  => null,
            'blur'       => null,
            'brightness' => null,
            'background' => null,
            'sizes'      => []
        ];
    }

    /**
     * Get image link by size
     * @param $image
     * @param null $size
     * @param bool $first
     * @return string
     */
    public function linkImage($image, $size = null, $first = false): string
    {
        if (!$image) {
            return asset('img/placeholder.jpg');
        }

        if (!$size && !$first) {
            return asset('storage/' . Article::FOLDER_IMG . '/' . $image);
        }

        $settings = Cache::remember(
            'article_sizes',
            now()->addDay(),
            function () {
                return ArticleSettings::where('name', 'sizes')->first();
            }
        );

        if ($settings && !empty($settings->data['sizes'])) {
            $sortedSizes = collect($settings->data['sizes'])->sortBy('width');
            if ($first) {
                return asset('storage/' . Article::FOLDER_IMG . '/' . $sortedSizes->first()['name'] . '/' . $image);
            }

            if (array_key_exists($size, $settings->data['sizes'])) {
                return asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $image);
            } else {
                return asset('storage/' . Article::FOLDER_IMG . '/' . $sortedSizes->last()['name'] . '/' . $image);
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

        $settings = Cache::remember(
            'article_sizes',
            now()->addDay(),
            function () {
                return ArticleSettings::where('name', 'sizes')->first();
            }
        );

        if ($image && $settings && !empty($settings->data['sizes'])) {
            $sortedSizes = collect($settings->data['sizes'])->sortBy('width');
            foreach ($sortedSizes as $size => $sizes) {
                $src = asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $image);
                if ($srcset) {
                    $src .= ' ' . $sizes['width'] . 'w';
                }

                $images[] = $src;
            }
        }

        return $images;
    }

    public function prepareImgParams($imageSettings): array
    {
        $data['sizes'] = null;
        $data['resizeMethod'] = null;
        $data['greyscale'] = false;
        $data['blur'] = 1;
        $data['brightness'] = 0;
        $data['background'] = null;
        $data['optimize'] = false;

        if ($imageSettings && !empty($imageSettings->data['sizes'])) {
            $data['sizes'] = $imageSettings->data['sizes'];
            $data['resizeMethod'] = $imageSettings->data['action'];
            if (!empty($imageSettings->data['greyscale'])) {
                $data['greyscale'] = $imageSettings->data['greyscale'];
            }
            if (!empty($imageSettings->data['blur'])) {
                $data['blur'] = $imageSettings->data['blur'];
            }
            if (!empty($imageSettings->data['brightness'])) {
                $data['brightness'] = $imageSettings->data['brightness'];
            }
            if (!empty($imageSettings->data['background'])) {
                $data['background'] = $imageSettings->data['background'];
            }
            if (!empty($imageSettings->data['optimize'])) {
                $data['optimize'] = $imageSettings->data['optimize'];
            }
        }

        return $data;
    }

    /**
     * Delete image from all folders
     * @param string $image
     */
    public function deleteImages(string $image)
    {
        $directories = Storage::directories(Article::FOLDER_IMG);

        foreach ($directories as $directory) {
            Storage::delete($directory . '/' . $image);
        }
    }

    /**
     * Delete original image
     * @param string $image
     */
    public function deleteOriginalImage(string $image)
    {
        Storage::delete(Article::FOLDER_IMG . '/' . $image);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function prepareSizeSettingsToSave(Request $request): array
    {
        $defaultAction = ArticleSettings::RESIZE_CROP;
        $action = $request->get('action', $defaultAction);
        $blur = null;
        $brightness = null;
        if ($request->get('blur') >= 0 && $request->get('blur') <= 100) {
            $blur = $request->get('blur');
        }
        if ($request->get('brightness') >= -100 && $request->get('brightness') <= 100) {
            $brightness = $request->get('brightness');
        }

        $data = [
            'ratio'      => $request->get('ratio'),
            'ratios'     => $request->get('ratios'),
            'action'     => in_array($action, ArticleSettings::resizeMethods()) ? $action : $defaultAction,
            'greyscale'  => $request->get('greyscale'),
            'blur'       => $blur,
            'brightness' => $brightness,
            'background' => $request->get('background'),
            'optimize'   => $request->get('optimize'),
        ];
        foreach ($request->get('sizes') as $size) {
            $data['sizes'][$size['name']] = [
                'name'   => $size['name'],
                'width'  => $size['width'] > 0 ? $size['width'] : null,
                'height' => $size['height'] > 0 ? $size['height'] : null
            ];
        }

        return $data;
    }
}
