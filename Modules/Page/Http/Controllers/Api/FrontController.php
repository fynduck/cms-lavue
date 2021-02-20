<?php

namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Modules\Page\Entities\Page;
use Modules\Page\Transformers\PageClientResource;

class FrontController extends Controller
{
    public function findPage($slug): PageClientResource
    {
        $response = null;
        if ($slug != 'home') {
            $page = Page::getPageBySlug($slug);
        } else {
            $page = Page::getDefault();
        }

        if (!$page || ($page->module && !checkModule($page->module))) {
            $page = Page::getPageByMethod('not_found');
        }

        return new PageClientResource($page);
    }
}