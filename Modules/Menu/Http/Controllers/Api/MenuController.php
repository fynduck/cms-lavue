<?php

namespace Modules\Menu\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Fynduck\FilesUpload\PrepareFile;
use Illuminate\Http\Request;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Http\Requests\MenuValidate;
use Modules\Menu\Services\MenuService;
use Modules\Menu\Transformers\MenuFormResource;
use Modules\Menu\Transformers\MenuListResource;

class MenuController extends AdminController
{
    protected $targets = [];

    protected $positions = [];

    protected $parents = [];

    public function __construct()
    {
        $this->middleware('admin:view');

        $this->positions = Menu::positions();

        $this->parents = Menu::leftJoin('menu_trans', 'menus.id', '=', 'menu_trans.menu_id')
            ->where('lang_id', config('app.locale_id'))
            ->get(['title', 'menus.id', 'position'])
            ->groupBy('position');

        $this->targets = Menu::targets();
    }

    /**
     * Lists menu for position
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $menu = Menu::leftJoin('menu_trans', 'menus.id', '=', 'menu_trans.menu_id')
            ->select('menus.id', 'menus.position', 'menus.type_page', 'menus.page_id', 'menus.image', 'menus.sort', 'menu_trans.title', 'menu_trans.lang_id', 'menu_trans.active')
            ->filter($request)->paginate(25);

        return MenuListResource::collection($menu);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MenuValidate $request
     * @param MenuService $menuService
     * @return bool
     * @throws \Exception
     */
    public function store(MenuValidate $request, MenuService $menuService)
    {
        $nameImages = $menuService->saveImages($request);

        /**
         * Create menu
         */
        \DB::beginTransaction();
        $menu = $menuService->addUpdate($request, $nameImages);

        $menuService->addUpdateTrans($menu, $request->get('items'));

        if ($request->get('show_page'))
            $menuService->showOn($menu->id, $request->get('show_page'));

        \DB::commit();

        return true;
    }

    public function show($id)
    {
        $item = Menu::find($id);

        $additional = [
            'positions'          => $this->positions,
            'parents'            => $this->parents,
            'targets'            => $this->targets
        ];

        if (!$item)
            $item = new Menu();

        return (new MenuFormResource($item))->additional($additional);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuValidate $request
     * @param MenuService $menuService
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function update(MenuValidate $request, MenuService $menuService, $id)
    {
        $nameImages = $menuService->saveImages($request);

        /**
         * Update menu
         */
        \DB::beginTransaction();
        $menu = $menuService->addUpdate($request, $nameImages, $id);

        $menuService->addUpdateTrans($menu, $request->get('items'));

        $menuService->showOn($menu->id, $request->get('show_page'));
        \DB::commit();

        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function destroy($id, Request $request)
    {
        $menu = Menu::find($id);
        PrepareFile::deleteImages(Menu::FOLDER_IMG, $menu->image, Menu::getSizes());
        if ($request->get('image')) {
            $menu->image = '';
            $menu->save();
        } else {
            return !$menu->delete();
        }

        return true;
    }
}
