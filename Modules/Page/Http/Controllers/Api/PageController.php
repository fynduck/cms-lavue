<?php

namespace Modules\Page\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Language\Entities\Language;
use Modules\Menu\Entities\Menu;
use Modules\Page\Entities\Page;
use Modules\Page\Http\Requests\PageValidate;
use Modules\Page\Services\PageService;
use Modules\Page\Transformers\PageFormResource;
use Modules\Page\Transformers\PageListResource;

class PageController extends AdminController
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $sortBy = $request->get('sortBy', 'updated_at');
        $sort = $request->get('sortDesc') ? 'DESC' : 'ASC';

        $pages = Page::leftJoin('page_trans', 'pages.id', '=', 'page_trans.page_id')
            ->filter($request)
            ->orderBy($sortBy, $sort)
            ->paginate(25);

        $languages = Language::whereActive(1)->pluck('name', 'id');

        return PageListResource::collection($pages)->additional(['languages' => $languages]);
    }

    /**
     * Store a newly created resource in storage.
     * @param PageValidate $request
     * @return bool
     */
    public function store(PageValidate $request): bool
    {
        \DB::beginTransaction();
        /**
         * Create page
         */
        $page = PageService::addUpdate($request);

        /**
         * Create page trans
         */
        PageService::addUpdateTrans($page->id, $request->get('items'));

        \DB::commit();

        return true;
    }

    /**
     * @param $id
     * @return PageFormResource
     */
    public function show($id): PageFormResource
    {
        $item = Page::find($id);

        if (!$item)
            $item = new Page();

        return (new PageFormResource($item));
    }

    /**
     * Update the specified resource in storage.
     * @param PageValidate $request
     * @param $id
     * @return bool
     */
    public function update(PageValidate $request, $id): bool
    {
        \DB::beginTransaction();
        /**
         * Create page
         */
        $page = PageService::addUpdate($request, $id);

        /**
         * Create page trans
         */
        PageService::addUpdateTrans($page->id, $request->get('items'));

        \DB::commit();

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function destroy($id): bool
    {
        $page = Page::find($id);

        if (!$page->method) {
            $page->delete();
            Menu::where('type_page', 'page')->where('page_id', $id)->delete();
        }

        return true;
    }
}
