<?php

namespace Modules\Settings\Http\Controllers\Api;

use App\Services\SiteMapService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Modules\Settings\Entities\Pagination;
use Modules\Settings\Entities\Settings;
use Modules\Settings\Entities\Social;
use Modules\Settings\Http\Requests\PaginateValidate;
use Modules\Settings\Http\Requests\SettingsPost;
use Modules\Settings\Http\Requests\SocialValidate;
use Modules\Settings\Services\SettingsService;
use Modules\Settings\Transformers\PaginateResource;
use Modules\Settings\Transformers\SocialsResource;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin:view');
    }

    /**
     * Contacts page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(SettingsService::formatShowSettings());
    }

    /**
     * @param SettingsPost|Request $request
     * @return mixed
     */
    public function store(SettingsPost $request)
    {
        foreach ($request->get('items') as $lang => $item) {
            foreach ($item as $key => $value) {
                $item = Settings::updateOrCreate([
                    'key'  => $key,
                    'lang' => $lang
                ], [
                        'value' => $value,
                    ]
                );

                if (!$item)
                    return false;
            }
        }

        Cache::forget('settings');

        return response()->json(SettingsService::formatShowSettings());
    }

    /**
     * Add custom style
     * @return \Illuminate\Http\JsonResponse
     */
    public function css()
    {
        $css = '';
        if (File::exists(base_path('front/static/css/custom.css')))
            $css = file_get_contents(base_path('front/static/css/custom.css'));

        return response()->json($css);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function saveCss(Request $request)
    {
        return file_put_contents(base_path('front/static/css/custom.css'), $request->get('css'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function config()
    {
        $config = file_get_contents(base_path('.env'));
        $config = explode("\n", $config);
        $configs = [];
        foreach ($config as $conf) {
            $aux = explode('=', $conf);
            if ($aux[0] && !empty($aux[1]))
                $configs[] = [
                    'key'   => $aux[0],
                    'value' => $aux[1]
                ];
        }

        return response()->json($configs);
    }

    /**
     * @param Request $request
     * @return false|int
     */
    protected function saveEnv(Request $request)
    {
        $data = [];
        // replace space in string
        foreach ($request->all() as $item)
            $data[$item['key']] = str_replace(' ', '_', trim($item['value']));

        $config = file_get_contents(base_path('.env'));
        $config = explode("\n", $config);
        $newConfigs = [];
        foreach ($config as $conf) {
            $aux = explode('=', $conf);

            if ($aux[0] && !empty($aux[1] && array_key_exists($aux[0], $data)))
                $newConfigs[] = $aux[0] . '=' . $data[$aux[0]];
            else
                $newConfigs[] = $conf;
        }

        return file_put_contents(base_path('.env'), implode("\n", $newConfigs));
    }

    /** Social lists
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function socials()
    {
        return SocialsResource::collection(Social::all());
    }

    /** Save socials
     * @param SocialValidate $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    protected function saveSocials(SocialValidate $request)
    {
        Social::whereNotIn('name', collect($request->all())->pluck('name')->toArray())->delete();

        SettingsService::addUpdateSocials($request->all());

        Cache::forget('socials');

        return SocialsResource::collection(Social::all());
    }

    /** Return values for pagination
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function pagination()
    {
        return PaginateResource::collection(Pagination::all());
    }

    /** Save value pagination
     * @param PaginateValidate $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    protected function savePagination(PaginateValidate $request)
    {
        $on = collect($request->all())->pluck('on')->toArray();
        $for = collect($request->all())->pluck('for')->toArray();

        Pagination::whereNotIn('on', $on)->whereNotIn('for', $for)->delete();

        SettingsService::addUpdatePagination($request);

        return PaginateResource::collection(Pagination::all());

    }

    public function sitemap()
    {
        return view('settings::sitemap');
    }

    protected function sitemapGenerate(SiteMapService $siteMap)
    {
        $siteMap->generateMap();

        return back()->with('success', 'Generate success');
    }
}
