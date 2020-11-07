<?php

namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;
use Modules\Page\Transformers\PageClientResource;

class FrontController extends Controller
{
    public function findPage($slug)
    {
        $response = null;
        if ($slug != 'home')
            $page = Page::getPageBySlug($slug);
        else
            $page = Page::getDefault();

        if (!$page)
            $page = Page::getPageByMethod('not_found');

        $pageLang = PageTrans::where('page_id', $page->page_id)
            ->where('active', 1)
            ->pluck('lang_id')->toArray();

        return (new PageClientResource($page))->additional(['page_lang' => $pageLang]);
    }
}