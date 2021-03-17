<?php

namespace Modules\Banner\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Entities\BannerSettings;
use Modules\Banner\Transformers\BannerResource;

class FrontController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getBanner(Request $request)
    {
        $data = [];

        $position = $request->get('position', 'top');

        if ($request->get('page_id')) {
            $page_id = $request->get('page_id');
            $page_type = $request->get('type', 'page');
            $data = Banner::getByPageId($page_id, $page_type, $position)->betweenDate()->get();
        }

        $settingsName = $position . '_sizes';
        $settings = Cache::remember(
            $settingsName,
            now()->addDay(),
            function () use ($settingsName) {
                return BannerSettings::where('name', $settingsName)->first();
            }
        );
        $additional = [];
        if ($settings) {
            $additional = [
                'carousel_settings' => [
                    'interval'   => $settings->data['interval'] ?? 3000,
                    'indicators' => $settings->data['indicators'] ?? true,
                    'nav'        => $settings->data['nav'] ?? true,
                ]
            ];
        }
        return BannerResource::collection($data)->additional($additional);
    }
}
