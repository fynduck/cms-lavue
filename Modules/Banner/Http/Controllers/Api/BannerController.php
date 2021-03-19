<?php

namespace Modules\Banner\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Banner\Entities\Banner;
use Illuminate\Http\Request;
use Modules\Banner\Entities\BannerSettings;
use Modules\Banner\Http\Requests\BannerValidate;
use Modules\Banner\Http\Requests\SizeValidate;
use Modules\Banner\Jobs\RegenerateImageSizes;
use Modules\Banner\Services\BannerService;
use Modules\Banner\Transformers\BannerFormResource;
use Modules\Banner\Transformers\BannerListResource;
use Modules\Language\Entities\Language;
use Throwable;

class BannerController extends AdminController
{
    protected $positions;

    protected $targets;

    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->middleware('admin');
        $this->positions = Banner::getPositions();
        $this->targets = Banner::targets();

        $this->bannerService = $bannerService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $banners = Banner::leftJoin('banner_trans', 'banners.id', '=', 'banner_trans.banner_id')
            ->filter($request)
            ->orderBy('priority')
            ->orderBy('updated_at', 'DESC')->paginate(25);

        $sizeSettings = [];
        foreach ($this->positions as $type => $title) {
            $sizeSettings[$type] = $this->bannerService->sizeSettings($type . '_sizes');
        }

        $additional = [
            'languages' => Language::whereActive(1)->pluck('name', 'id'),
            'settings'  => array_filter($sizeSettings)
        ];

        return BannerListResource::collection($banners)->additional($additional);
    }

    /**
     * Store a newly created resource in storage.
     * @param BannerValidate $request
     * @param BannerService $bannersService
     * @return bool
     * @throws Exception
     */
    public function store(BannerValidate $request, BannerService $bannersService): bool
    {
        /**
         * Save image(s)
         */
        $nameImages = $bannersService->saveImages($request);

        /**
         * Create banner
         */
        DB::beginTransaction();
        $banner = $bannersService->addUpdate($request, $nameImages);

        $bannersService->addUpdateTrans($banner, $request->get('items'));

        /**
         * Add show pages
         */
        $bannersService->addUpdateShow($banner->id, $request->get('pagesShow'));
        DB::commit();

        return true;
    }

    /**
     * @param $id
     * @return BannerFormResource
     */
    public function show($id): BannerFormResource
    {
        $item = Banner::find($id);

        if (!$item) {
            $item = new Banner();
        }

        $additional = [
            'positions' => $this->positions,
            'targets'   => $this->targets
        ];

        return (new BannerFormResource($item))->additional($additional);
    }

    /**
     * Update the specified resource in storage.
     * @param BannerValidate $request
     * @param BannerService $bannersService
     * @param Banner $banner
     * @return bool
     * @throws Throwable
     */
    public function update(BannerValidate $request, BannerService $bannersService, Banner $banner): bool
    {
        /**
         * Save image(s)
         */
        $nameImages = $bannersService->saveImages($request);

        /**
         * Update banner
         */
        DB::beginTransaction();
        $bannersService->addUpdate($request, $nameImages, $banner->id);

        $bannersService->addUpdateTrans($banner, $request->get('items'));

        /**
         * Add show pages
         */
        $bannersService->addUpdateShow($banner->id, $request->get('pagesShow'));
        DB::commit();

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param Banner $banner
     * @return bool
     * @throws Exception
     */
    public function destroy(Request $request, Banner $banner): bool
    {
        if ($request->get('image')) {
            if ($banner->image === $request->get('image')) {
                $this->bannerService->deleteImages($banner->image);
                $this->bannerService->deleteOriginalImage($banner->image);
                $banner->image = '';
            }
            if ($banner->mobile_image === $request->get('image')) {
                $this->bannerService->deleteImages($banner->mobile_image);
                $this->bannerService->deleteOriginalImage($banner->mobile_image);
                $banner->mobile_image = '';
            }
            $banner->save();
        } else {
            $banner->delete();
        }

        return true;
    }

    /**
     * Save settings
     * @param SizeValidate $request
     * @return bool
     */
    public function saveSettings(SizeValidate $request): bool
    {
        $data = $this->bannerService->prepareSizeSettingsToSave($request);

        $type = $request->get('position') . '_sizes';
        BannerSettings::updateOrCreate(['name' => $type], ['data' => $data]);

        RegenerateImageSizes::dispatch($request->get('position'), $type);

        Cache::forget("banner_$type");
        Cache::forget($type);

        return true;
    }
}
