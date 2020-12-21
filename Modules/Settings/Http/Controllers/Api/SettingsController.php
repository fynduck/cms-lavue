<?php

namespace Modules\Settings\Http\Controllers\Api;

use App\Services\SiteMapService;
use Fynduck\FilesUpload\UploadFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(SettingsService::formatShowSettings());
    }

    /**
     * @param SettingsPost|Request $request
     * @return JsonResponse
     */
    public function store(SettingsPost $request)
    {
        foreach ($request->get('items') as $lang => $item) {
            foreach ($item as $key => $value) {
                if ($key == 'logo') {
                    if ((bool)preg_match("/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).base64,.*/", $value)) {
                        $value = UploadFile::file($value)
                            ->setFolder(Settings::FOLDER_IMG)
                            ->save();
                    }
                }
                $item = Settings::updateOrCreate([
                    'key'  => $key,
                    'lang' => $lang
                ], [
                        'value' => $value,
                    ]
                );

                if (!$item)
                    return response()->json(trans('Settings::admin.data_not_save'));
            }
        }

        Cache::forget('settings');

        return response()->json(SettingsService::formatShowSettings());
    }

    /**
     * Add custom style
     * @return JsonResponse
     */
    public function css(): JsonResponse
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
    public function saveCss(Request $request): bool
    {
        return file_put_contents(base_path('front/static/css/custom.css'), $request->get('css'));
    }

    /**
     * @return JsonResponse
     */
    public function config(): JsonResponse
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
     * @return AnonymousResourceCollection
     */
    public function socials(): AnonymousResourceCollection
    {
        return SocialsResource::collection(Social::all());
    }

    /** Save socials
     * @param SocialValidate $request
     * @return AnonymousResourceCollection
     */
    protected function saveSocials(SocialValidate $request): AnonymousResourceCollection
    {
        Social::whereNotIn('name', collect($request->all())->pluck('name')->toArray())->delete();

        SettingsService::addUpdateSocials($request->all());

        Cache::forget('socials');

        return SocialsResource::collection(Social::all());
    }

    /** Return values for pagination
     * @return AnonymousResourceCollection
     */
    public function pagination(): AnonymousResourceCollection
    {
        return PaginateResource::collection(Pagination::all());
    }

    /** Save value pagination
     * @param PaginateValidate $request
     * @return AnonymousResourceCollection
     */
    protected function savePagination(PaginateValidate $request): AnonymousResourceCollection
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

    protected function sitemapGenerate(SiteMapService $siteMap): RedirectResponse
    {
        $siteMap->generateMap();

        return back()->with('success', 'Generate success');
    }
}
