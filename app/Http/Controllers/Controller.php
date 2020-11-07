<?php

namespace App\Http\Controllers;

use App\Services\SetGlobal;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Modules\Currency\Entities\Currency;
use Modules\Menu\Entities\Menu;
use Modules\Page\Entities\Page;
use Modules\Settings\Entities\Settings;
use Modules\Settings\Entities\Social;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $data;

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

        /**
         * urls pages
         */
        $this->data['urlsPages'] = Cache::remember('urls_pages_' . config('app.locale_id'), now()->addHours(5), function () {
            return Page::getSlugAllStaticPages()->toArray();
        });

        /**
         * settings
         */
        $this->data['settings'] = Cache::remember('settings', now()->addHour(), function () {
            return Settings::getByKey([
                'name_site',
                'city',
                'street',
                'postal_code',
                'contact_phone',
                'contact_email',
                'analytics',
                'analytics_top',
                'bank_info',
                'work_time',
            ])->toArray();
        });

        /**
         * menus
         */
        $this->data['menus'] = Cache::remember('site_menus_' . config('app.locale_id'), now()->addDay(), function () {
            return Menu::getMenuByPosition([Menu::TOP_MENU, Menu::FOOTER_LINKS], 1)
                ->with(['children', 'children.trans'])
                ->get()
                ->groupBy('position');
        });

        /**
         * default page - home page
         */
        $this->data['page'] = Cache::remember('home_page_' . config('app.locale_id'), now()->addDay(), function () {
            return Page::getDefault('home');
        });
        $this->data = SetGlobal::setMeta($this->data, $this->data['page']);
        $this->data['breadcrumbs'][] = ['url' => route('home'), 'title' => trans('global.home')];
    }
}
