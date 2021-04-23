<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SetGlobal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Article\Entities\Article;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;
use Modules\Settings\Entities\Pagination;

class FrontController extends Controller
{
    protected function contacts($data)
    {
        $company = null;
        $data['address'] = isset($data['settings']['contact_address']) ? $data['settings']['contact_address'] : '';

        return view('page::contacts', $data);
    }

    protected function about($data)
    {
        return view('page::about', $data);
    }

    protected function hits($data)
    {
        return view('page::show_page_hits_promo', $data);
    }

    protected function sales($data)
    {
        return view('page::show_page_hits_promo', $data);
    }
}
