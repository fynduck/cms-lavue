<?php

namespace Modules\Banner\Services;

use Fynduck\FilesUpload\ManipulationImage;
use Fynduck\FilesUpload\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Entities\BannerSettings;
use Modules\Banner\Entities\BannerShow;
use Modules\Banner\Entities\BannerTrans;
use Modules\Banner\Traits\BannerImageTrait;

/**
 * Created by PhpStorm.
 * User: stass
 * Date: 25.05.2017
 * Time: 12:20
 */
class BannerService
{
    use BannerImageTrait;

    /**
     * @param Request $request
     * @param array $imagesName
     * @param int|null $id
     * @return mixed
     */
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

    /**
     * @param Banner $banner
     * @param $items
     */
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
            'encode'     => null,
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
     * @param Request $request
     * @return array
     */
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
                ->setEncodeFormat($imageSettings['encode'])
                ->save($imageSettings['resizeMethod']);

            $this->generateReserveImg($imageSettings, $nameImages['imageName']);
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
                ->setEncodeFormat($imageSettings['encode'])
                ->save($imageSettings['resizeMethod']);

            $this->generateReserveImg($imageSettings, $nameImages['imageMobileName']);
        }

        return $nameImages;
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
            'encode'     => in_array($request->get('encode'), Banner::FORMATS) ? $request->get('encode') : null,
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
            $path = Storage::get(Banner::FOLDER_IMG . '/' . $imageName);

            $biggestSize = collect($data['sizes'])->sortBy('width')->sortBy('height')->last();
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
            ->setFolder(Banner::FOLDER_IMG)
            ->setGreyscale($data['greyscale'])
            ->setBlur($data['blur'])
            ->setBrightness($data['brightness'])
            ->setBackground($data['background'])
            ->setOptimize($data['optimize'])
            ->setEncodeFormat($data['encode'])
            ->save($data['resizeMethod']);
    }
}
