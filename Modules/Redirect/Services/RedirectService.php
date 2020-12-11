<?php

namespace Modules\Redirect\Services;

use Modules\Redirect\Entities\Redirect;

class RedirectService
{
    public function checkHasRedirect($old_url, $params, $langPrefix = null)
    {
        if (!$langPrefix)
            $langPrefix = config('app.locale_prefix');

        $hasRedirect = $this->requestHasRedirect($old_url);
        if ($hasRedirect)
            return ['redirectTo' => $hasRedirect->to, 'status' => $hasRedirect->status_code];

        /**
         * Check by slug redirect
         */
        $status = null;
        foreach (array_filter($params) as $key => $slug) {
            $url = array_filter([$langPrefix, $slug]);
            $slugRedirect = $this->requestHasRedirect($url);

            if ($slugRedirect) {
                $params[$key] = $slugRedirect->to;
                $status = $slugRedirect->status_code;
            }
        }

        if ($status) {
            $params = array_merge($params, request()->query());

            return ['redirectTo' => route('pages', $params), 'status' => $status];
        }

        return false;
    }

    private function requestHasRedirect($from)
    {
        return Redirect::where('from', $from)->where('active', 1)->first();
    }
}
