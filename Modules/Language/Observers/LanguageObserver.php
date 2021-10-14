<?php

namespace Modules\Language\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;

class LanguageObserver
{
    /**
     * Handle the language "saved" event.
     *
     * @param Language $language
     * @return void
     */
    public function saved(Language $language)
    {
        if ($language->default) {
            Language::where('id', '<>', $language->id)->where('default', 1)->where('active', 1)->update(['default' => 0]);
        } else {
            $default = Language::where('default', 1)->where('active', 1)->first();
            if (!$default) {
                Language::where('active', 1)->first()->update(['default' => 1]);
            }
        }


        Cache::forget('languages');
    }

    /**
     * Handle the language "deleted" event.
     *
     * @param Language $language
     * @return void
     */
    public function deleted(Language $language)
    {
        Cache::forget('languages');
    }
}
