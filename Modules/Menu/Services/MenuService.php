<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 31.03.2017
 * Time: 12:40
 */

namespace Modules\Menu\Services;

use Fynduck\FilesUpload\PrepareFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Entities\MenuSettings;
use Modules\Menu\Entities\MenuShow;
use Modules\Menu\Entities\MenuTrans;
use Modules\Menu\Http\Requests\MenuValidate;

class MenuService
{
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

        return Menu::updateOrCreate([
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
                'sort'       => (int)$request->get('sort'),
            ]
        );
    }

    public function addUpdateTrans(Menu $menu, array $items)
    {
        foreach ($items as $lang_id => $item) {

            $menuTrans = MenuTrans::updateOrCreate([
                'menu_id' => $menu->id,
                'lang_id' => $lang_id
            ], [
                    'title'            => $item['title'],
                    'additional_title' => $item['additional_title'],
                    'link'             => $item['link'],
                    'description'      => isset($item['description']) ? $item['description'] : '',
                    'lang_id'          => $lang_id,
                    'active'           => $item['active'],
                ]
            );

            if (!$menuTrans)
                $menu->delete();
        }
    }

    public function showOn(int $id, array $show_on)
    {
        MenuShow::where('menu_id', $id)->delete();

        foreach ($show_on as $item) {
            $item = explode('_', $item);

            MenuShow::create([
                'menu_id'   => $id,
                'show_on'   => $item[1],
                'show_type' => $item[0],
            ]);
        }
    }


    /**
     * Save all images for articles
     * @param Request $request
     * @return array
     */
    public function saveImages(Request $request)
    {
        $nameImages = [
            'imageName' => null
        ];
        $imgName = null;
        if ($request->get('items')[config('app.fallback_locale_id')]['title'])
            $imgName = $request->get('items')[config('app.fallback_locale_id')]['title'];

        if ($request->get('image')) {
            if (!Str::contains($request->get('image'), Menu::FOLDER_IMG)) {
                $menuSettings = MenuSettings::latest()->first();
                $sizes = $menuSettings->sizes;
                $resizeMethod = $menuSettings->resize;
                $nameImages['imageName'] = PrepareFile::uploadBase64(Menu::FOLDER_IMG, 'image', $request->get('image'), $imgName, $request->get('old_image'), $sizes, $resizeMethod);
            } else {
                $nameImages['imageName'] = $request->get('old_image');
            }
        }

        return $nameImages;
    }
}
