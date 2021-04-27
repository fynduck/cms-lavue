<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 31.03.2017
 * Time: 12:40
 */

namespace Modules\Menu\Services;

use Fynduck\FilesUpload\ManipulationImage;
use Fynduck\FilesUpload\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Entities\MenuSettings;
use Modules\Menu\Entities\MenuShow;
use Modules\Menu\Entities\MenuTrans;
use Modules\Menu\Http\Requests\MenuValidate;
use Modules\Menu\Traits\MenuImageTrait;

class MenuService
{
    use MenuImageTrait;

    /**
     * @param MenuValidate $request
     * @param array $imagesName
     * @param int|null $id
     * @return \Illuminate\Database\Eloquent\Model|Menu
     */
    public function addUpdate(MenuValidate $request, array $imagesName, int $id = null)
    {
        $type = '';
        $page_id = 0;
        if ($request->get('to_page')) {
            $type = explode('_', $request->get('to_page'))[0];
            $page_id = explode('_', $request->get('to_page'))[1];
        }

        return Menu::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'parent_id'  => $request->get('parent_id'),
                'target'     => $request->get('target'),
                'attributes' => $request->get('attributes'),
                'type_page'  => $type,
                'page_id'    => $page_id,
                'image'      => $imagesName['imageName'],
                'icon'       => $request->get('icon'),
                'position'   => $request->get('position'),
                'nofollow'   => $request->has('nofollow') ? 1 : 0,
                'priority'   => (int)$request->get('priority'),
            ]
        );
    }

    /**
     * @param Menu $menu
     * @param array $items
     * @throws \Exception
     */
    public function addUpdateTrans(Menu $menu, array $items)
    {
        foreach ($items as $lang_id => $item) {
            $menuTrans = MenuTrans::updateOrCreate(
                [
                    'menu_id' => $menu->id,
                    'lang_id' => $lang_id
                ],
                [
                    'title'            => $item['title'],
                    'additional_title' => $item['additional_title'],
                    'link'             => $item['link'],
                    'description'      => isset($item['description']) ? $item['description'] : '',
                    'lang_id'          => $lang_id,
                    'active'           => $item['active'],
                ]
            );

            if (!$menuTrans) {
                $menu->delete();
            }
        }
    }

    /**
     * @param int $id
     * @param array $show_on
     */
    public function showOn(int $id, array $show_on)
    {
        MenuShow::where('menu_id', $id)->delete();

        foreach ($show_on as $item) {
            $item = explode('_', $item);

            MenuShow::create(
                [
                    'menu_id'   => $id,
                    'show_on'   => $item[1],
                    'show_type' => $item[0],
                ]
            );
        }
    }

    /**
     * Save all images for articles
     * @param Request $request
     * @return array
     */
    public function saveImages(Request $request): array
    {
        $nameImages = [
            'imageName' => null
        ];
        $imgName = null;
        if ($request->get('items')[config('app.fallback_locale_id')]['title']) {
            $imgName = $request->get('items')[config('app.fallback_locale_id')]['title'];
        }

        if ($request->get('image') && $this->isBase64($request->get('image'))) {
            $positionSize = $request->get('position') . '_sizes';
            $settings = Cache::remember(
                "menu_$positionSize",
                now()->addDay(),
                function () use ($positionSize) {
                    return MenuSettings::where('name', $positionSize)->first();
                }
            );
            $data = $this->prepareImgParams($settings);
            $nameImages['imageName'] = UploadFile::file($request->get('image'))
                ->setFolder(Menu::FOLDER_IMG)
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
        } else {
            $nameImages['imageName'] = $request->get('old_image');
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
            "menu_$nameSize",
            now()->addDay(),
            function () use ($nameSize) {
                return MenuSettings::where('name', $nameSize)->first();
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
     * @param Request $request
     * @return array
     */
    public function prepareSizeSettingsToSave(Request $request): array
    {
        $defaultAction = MenuSettings::RESIZE_CROP;
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
            'action'     => in_array($action, MenuSettings::resizeMethods()) ? $action : $defaultAction,
            'encode'     => in_array($request->get('encode'), Menu::FORMATS) ? $request->get('encode') : null,
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
            $path = Storage::get(Menu::FOLDER_IMG . '/' . $imageName);

            $biggestSize = collect($data['sizes'])->sortBy('width')->sortBy('height')->last();
            $data['sizes'] = [];
            $data['sizes'][$biggestSize['name']] = $biggestSize;
            $data['encode'] = explode('_', $imageName)[0];

            $this->generateImageSizes($path, $data, $imageName);
        }
    }

    /**
     * Delete image from all folders
     * @param string $image
     */
    public function deleteImages(string $image)
    {
        $directories = Storage::directories(Menu::FOLDER_IMG);

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
        Storage::delete(Menu::FOLDER_IMG . '/' . $image);
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
            ->setFolder(Menu::FOLDER_IMG)
            ->setGreyscale($data['greyscale'])
            ->setBlur($data['blur'])
            ->setBrightness($data['brightness'])
            ->setBackground($data['background'])
            ->setOptimize($data['optimize'])
            ->setEncodeFormat($data['encode'])
            ->save($data['resizeMethod']);
    }
}
