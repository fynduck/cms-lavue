<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 9/26/18
 * Time: 12:49 AM
 */

namespace Modules\Article\Services;

use Fynduck\FilesUpload\ManipulationImage;
use Fynduck\FilesUpload\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Traits\ArticleImageTrait;

class ArticleService
{
    use ArticleImageTrait;

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

    /**
     * @param int $id
     * @param array $items
     */
    public function addUpdateTrans(int $id, array $items)
    {
        foreach ($items as $lang_id => $item) {
            ArticleTrans::updateOrCreate(
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
        }
    }

    /**
     * @param $limit
     * @param $typeName
     * @param $value
     * @param $q
     * @param $selected
     * @return mixed
     */
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

    /**
     * @param Request $request
     * @return array
     */
    public function saveImages(Request $request): array
    {
        $nameImages = [
            'imageName' => $request->get('old_image')
        ];
        $imgName = null;
        if ($request->get('items')[config('app.fallback_locale_id')]['title']) {
            $imgName = $request->get('items')[config('app.fallback_locale_id')]['title'];
        }

        $type = $request->get('type') . '_sizes';
        if ($request->get('image') && $this->isBase64($request->get('image'))) {
            $settings = Cache::remember(
                "article_$type",
                now()->addDay(),
                function () use ($type) {
                    return ArticleSettings::where('name', $type)->first();
                }
            );

            $data = $this->prepareImgParams($settings);
            $nameImages['imageName'] = UploadFile::file($request->get('image'))
                ->setFolder(Article::FOLDER_IMG)
                ->setName($imgName)
                ->setOverwrite($request->get('old_image'))
                ->setSizes($data['sizes'])
                ->setGreyscale($data['greyscale'])
                ->setBlur($data['blur'])
                ->setBrightness($data['brightness'])
                ->setBackground($data['background'])
                ->setOptimize($data['optimize'])
                ->setEncodeFormat($data['encode'])
                ->save($data['resizeMethod']);

            $this->generateReserveImg($data, $nameImages['imageName']);
        }

        return $nameImages;
    }

    /**
     * @param string $nameSize
     * @return array
     */
    public function sizeSettings(string $nameSize): array
    {
        $settings = Cache::remember(
            "article_$nameSize",
            now()->addDay(),
            function () use ($nameSize) {
                return ArticleSettings::where('name', $nameSize)->first();
            }
        );

        $data = $this->defaultSizeSettings();
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

            foreach ($this->defaultSizeSettings() as $key => $defaultSetting) {
                if (!array_key_exists($key, $data)) {
                    $data[$key] = $defaultSetting;
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function defaultSizeSettings(): array
    {
        return [
            'ratios'     => [
                'width'  => 0,
                'height' => 0,
            ],
            'ratio'      => false,
            'action'     => 'resize-crop',
            'encode'     => null,
            'optimize'   => null,
            'greyscale'  => null,
            'blur'       => null,
            'brightness' => null,
            'background' => null,
            'sizes'      => []
        ];
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
            'encode'     => in_array($request->get('encode'), Article::FORMATS) ? $request->get('encode') : null,
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

    /**
     * Generate reserve image size in case format is webp
     * @param array $data
     * @param string $imageName
     * @param bool $checkOriginalName
     */
    public function generateReserveImg(array $data, string $imageName, bool $checkOriginalName = true)
    {
        if ($data['encode'] === 'webp') {
            if ($checkOriginalName) {
                $imageName = $this->getOriginalImageName($imageName);
            }
            $path = Storage::get(Article::FOLDER_IMG . '/' . $imageName);

            $biggestSize = collect($data['sizes'])->sortBy('width')->sortBy('height')->last();
            $data['sizes'] = [];
            $data['sizes'][$biggestSize['name']] = $biggestSize;
            $data['encode'] = explode('_', $imageName)[0];

            $this->generateImageSizes($path, $data, $imageName);
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $imageName
     */
    public function generateImageSizes(string $path, array $data, string $imageName)
    {
        ManipulationImage::load($path)
            ->setSizes($data['sizes'])
            ->setName($imageName)
            ->setFolder(Article::FOLDER_IMG)
            ->setGreyscale($data['greyscale'])
            ->setBlur($data['blur'])
            ->setBrightness($data['brightness'])
            ->setBackground($data['background'])
            ->setOptimize($data['optimize'])
            ->setEncodeFormat($data['encode'])
            ->save($data['resizeMethod']);
    }
}
