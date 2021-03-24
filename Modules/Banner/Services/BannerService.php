<?php

namespace Modules\Banner\Services;

use Fynduck\FilesUpload\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Entities\BannerSettings;
use Modules\Banner\Entities\BannerShow;
use Modules\Banner\Entities\BannerTrans;

/**
 * Created by PhpStorm.
 * User: stass
 * Date: 25.05.2017
 * Time: 12:20
 */
class BannerService
{
    private $formats = ['jpeg', 'jpg', 'png', 'gif', 'webp'];

    public function addUpdate(Request $request, array $imagesName, int $id = null)
    {
        $type = '';
        $page_id = null;
        if ($request->get('toPage')) {
            $type = explode('_', $request->get('toPage'))[0];
            $page_id = explode('_', $request->get('toPage'))[1];
        }

        $image = $request->get('old_image');
        if (!empty($imagesName['imageName'])) {
            $image = $imagesName['imageName'];
        }

        $mobileImage = $request->get('old_mobile_image');
        if (!empty($imagesName['imageMobileName'])) {
            $mobileImage = $imagesName['imageMobileName'];
        }

        return Banner::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'image'        => $image,
                'mobile_image' => $mobileImage,
                'target'       => $request->get('target'),
                'type_page'    => $type,
                'page_id'      => $page_id,
                'position'     => $request->get('position'),
                'link'         => $request->get('link'),
                'priority'     => $request->get('priority') ? $request->get('priority') : 0,
                'date_from'    => $request->get('date_from'),
                'date_to'      => $request->get('date_to')
            ]
        );
    }

    public function addUpdateTrans(Banner $banner, $items)
    {
        foreach ($items as $lang => $item) {
            BannerTrans::updateOrCreate(
                [
                    'banner_id' => $banner->id,
                    'lang_id'   => $lang
                ],
                [
                    'title'       => $item['title'],
                    'description' => $item['description'],
                    'active'      => !empty($item['active']) ? 1 : 0,
                ]
            );
        }
    }

    /**
     * @param $id
     * @param array|null $show_pages
     */
    public function addUpdateShow($id, ?array $show_pages)
    {
        BannerShow::where('banner_id', $id)->delete();

        if ($show_pages) {
            foreach ($show_pages as $item) {
                if ($item) {
                    $item = explode('_', $item);
                    BannerShow::create(
                        [
                            'banner_id' => $id,
                            'type_page' => $item[0],
                            'page_id'   => $item[1]
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param $nameSize
     * @return array
     */
    public function sizeSettings($nameSize): array
    {
        $settings = Cache::remember(
            "banner_$nameSize",
            now()->addDay(),
            function () use ($nameSize) {
                return BannerSettings::where('name', $nameSize)->first();
            }
        );

        $data = $this->defaultSizeSettings();
        if ($settings && !empty($settings->data['sizes'])) {
            $data = $settings->data;
            $sizes = [];
            foreach ($settings->data['sizes'] as $size) {
                $sizes[] = [
                    'mobile' => $size['mobile'] ?? false,
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
            'optimize'   => null,
            'greyscale'  => null,
            'blur'       => null,
            'brightness' => null,
            'background' => null,
            'interval'   => 3000,
            'indicators' => 0,
            'nav'        => 1,
            'sizes'      => []
        ];
    }

    /**
     * Get image link by size
     * @param string|null $image
     * @param string $position
     * @param string|null $size
     * @param bool $first
     * @param bool $avg
     * @return string
     */
    public function linkImage(?string $image, string $position, string $size = null, bool $first = false, bool $avg = false): string
    {
        if (!$image) {
            return asset('img/placeholder.jpg');
        }

        if (!$size && !$first && !$avg) {
            return asset('storage/' . Banner::FOLDER_IMG . '/' . $image);
        }

        $settingsName = $position . '_sizes';
        $sizeSettings = Cache::remember(
            $settingsName,
            now()->addDay(),
            function () use ($settingsName) {
                return BannerSettings::where('name', $settingsName)->first();
            }
        );

        if ($sizeSettings && !empty($sizeSettings->data['sizes'])) {
            $sortedSizes = collect($sizeSettings->data['sizes'])->sortBy('width');
            if ($first) {
                return asset('storage/' . Banner::FOLDER_IMG . '/' . $sortedSizes->first()['name'] . '/' . $image);
            }

            if ($avg) {
                $avgKey = $sortedSizes->count() / 2 - 1;
                if ($avgKey > 0) {
                    return asset('storage/' . Banner::FOLDER_IMG . '/' . $sortedSizes->values()[$avgKey]['name'] . '/' . $image);
                } else {
                    return asset('storage/' . Banner::FOLDER_IMG . '/' . $sortedSizes->first()['name'] . '/' . $image);
                }
            }

            if (array_key_exists($size, $sizeSettings->data['sizes'])) {
                return asset('storage/' . Banner::FOLDER_IMG . '/' . $size . '/' . $image);
            } else {
                return asset('storage/' . Banner::FOLDER_IMG . '/' . $sortedSizes->last()['name'] . '/' . $image);
            }
        }

        return asset('storage/' . Banner::FOLDER_IMG . '/' . $image);
    }

    /**
     * Get all links with image size
     * @param string $image
     * @param string $position
     * @param bool $srcset
     * @param bool $mobile
     * @return array
     */
    public function linkImages(string $image, string $position, bool $srcset = true, bool $mobile = false): array
    {
        $images = [];

        $settingsName = $position . '_sizes';
        $imageSettings = Cache::remember(
            $settingsName,
            now()->addDay(),
            function () use ($settingsName) {
                return BannerSettings::where('name', $settingsName)->first();
            }
        );

        if ($image && $imageSettings && !empty($imageSettings->data['sizes'])) {
            $sortedSizes = collect($imageSettings->data['sizes'])->sortBy('width');
            foreach ($sortedSizes as $size => $sizes) {
                $checkFileExists = Storage::exists(Banner::FOLDER_IMG . '/' . $size . '/' . $image);
                if (((!$mobile && !$sizes['mobile']) || $mobile) && $checkFileExists) {
                    $src = asset('storage/' . Banner::FOLDER_IMG . '/' . $size . '/' . $image);
                    if ($srcset) {
                        $src .= ' ' . $sizes['width'] . 'w';
                    }

                    $images[] = $src;
                }
            }
        }

        return $images;
    }

    public function saveImages(Request $request): array
    {
        $nameImages = [
            'imageName'       => $request->get('old_image'),
            'imageMobileName' => $request->get('old_mobile_image')
        ];
        if (!$request->get('image') && !$request->get('mobile_image')) {
            return $nameImages;
        }

        $imgName = null;
        if ($request->get('items')[config('app.fallback_locale_id')]['title']) {
            $imgName = $request->get('items')[config('app.fallback_locale_id')]['title'];
        }

        $position = $request->get('position') . '_sizes';

        $settings = Cache::remember(
            $position,
            now()->addDay(),
            function () use ($position) {
                return BannerSettings::where('name', $position)->first();
            }
        );

        $imageSettings = $this->prepareImgParams($settings);

        $encode = 'webp';

        if ($request->get('image') && $this->isBase64($request->get('image'))) {
            $nameImages['imageName'] = UploadFile::file($request->get('image'))
                ->setFolder(Banner::FOLDER_IMG)
                ->setName($imgName)
                ->setOverwrite($request->get('old_image'))
                ->setSizes($imageSettings['sizes'])
                ->setGreyscale($imageSettings['greyscale'])
                ->setBlur($imageSettings['blur'])
                ->setBrightness($imageSettings['brightness'])
                ->setBackground($imageSettings['background'])
                ->setOptimize($imageSettings['optimize'])
                ->setEncodeFormat($encode)
                ->save($imageSettings['resizeMethod']);
        }

        if ($request->get('mobile_image') && $this->isBase64($request->get('mobile_image'))) {
            $mobileSizes = [];
            foreach ($imageSettings['sizes'] as $key => $size) {
                if ($size['mobile']) {
                    $mobileSizes[$key] = $size;
                }
            }
            if ($mobileSizes) {
                $imageSettings['sizes'] = $mobileSizes;
            }

            $nameImages['imageMobileName'] = UploadFile::file($request->get('mobile_image'))
                ->setFolder(Banner::FOLDER_IMG)
                ->setName($imgName . '_mobile')
                ->setOverwrite($request->get('old_mobile_image'))
                ->setSizes($imageSettings['sizes'])
                ->setGreyscale($imageSettings['greyscale'])
                ->setBlur($imageSettings['blur'])
                ->setBrightness($imageSettings['brightness'])
                ->setBackground($imageSettings['background'])
                ->setOptimize($imageSettings['optimize'])
                ->setEncodeFormat($encode)
                ->save($imageSettings['resizeMethod']);
        }

        return $nameImages;
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
     * @param string $file
     * @return bool
     */
    public function isBase64(string $file): bool
    {
        return (bool)preg_match("/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).base64,.*/", $file);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function prepareSizeSettingsToSave(Request $request): array
    {
        $defaultAction = BannerSettings::RESIZE_CROP;
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
            'action'     => in_array($action, BannerSettings::resizeMethods()) ? $action : $defaultAction,
            'greyscale'  => $request->get('greyscale'),
            'blur'       => $blur,
            'brightness' => $brightness,
            'background' => $request->get('background'),
            'optimize'   => $request->get('optimize'),
            'interval'   => $request->get('interval'),
            'indicators' => $request->get('indicators'),
            'nav'        => $request->get('nav')
        ];
        foreach ($request->get('sizes') as $size) {
            $data['sizes'][$size['name']] = [
                'mobile' => $size['mobile'] ?? false,
                'name'   => $size['name'],
                'width'  => $size['width'] > 0 ? $size['width'] : null,
                'height' => $size['height'] > 0 ? $size['height'] : null
            ];
        }

        return $data;
    }

    /**
     * Delete image from all folders
     * @param string $image
     */
    public function deleteImages(string $image)
    {
        $directories = Storage::directories(Banner::FOLDER_IMG);

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
        Storage::delete(Banner::FOLDER_IMG . '/' . $image);
    }

    public function getOriginalImageName($imageName): string
    {
        $explodedImage = explode('_', $imageName);
        $extension = $explodedImage[0];

        if (in_array($extension, $this->formats)) {
            $imageName = implode('_', $explodedImage);
            $explodedImage = explode('.', $imageName);
            array_pop($explodedImage);

            $imageName = implode('.', $explodedImage) . '.' . $extension;
        }

        return $imageName;
    }
}
