<?php


namespace Modules\Language\Traits;


use Illuminate\Http\Request;
use Modules\Language\Entities\Language;

trait LanguageTrait
{
    public function scopeFilter($query, Request $request)
    {
        if ($request->get('q')) {
            $search = $request->get('q');
            $query->where(
                function ($where) use ($search) {
                    $where->where('name', 'LIKE', "%$search%")
                        ->orWhere('slug', 'LIKE', "%$search%");
                }
            );
        }

        if ($request->get('active')) {
            $query->where('active', $request->get('active'));
        }
    }

    /**
     * Select item by id
     * @param $id
     * @param bool $active
     * @return mixed
     */
    static function lang($id, $active = false)
    {
        $request = Language::where('id', $id);
        if ($active) {
            $request->where('active', 1);
        }

        return $request->first();
    }

    /**
     * Select item by slug
     * @param $slug
     * @param int $active
     * @return mixed
     */
    static function langSlug($slug, $active = 1)
    {
        $request = Language::where('slug', $slug);
        if ($active) {
            $request->where('active', 1);
        }

        return $request->first();
    }

    /**
     * Select default language
     * @return mixed
     */
    static function defaultLang()
    {
        return Language::where('active', 1)->orderBy('default', 'DESC')->first();
    }

    /**
     * Select all
     * @param $id
     * @return mixed
     */
    static function firstLang($id)
    {
        $query = Language::orderBy('default', 'DESC')->orderBy('updated_at', 'DESC');
        if ($id) {
            $query->where('id', '<>', $id);
        }

        return $query->first();
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeDefault($query)
    {
        return $query->where('default', 1);
    }
}