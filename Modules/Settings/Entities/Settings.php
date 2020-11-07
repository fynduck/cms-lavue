<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Settings\Entities\Settings
 *
 * @property int $id
 * @property string $key
 * @property int $lang
 * @property string $value
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Settings whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Settings whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Settings whereValue($value)
 * @mixin \Eloquent
 */
class Settings extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'key', 'value', 'lang'
    ];
    /**
     * Get settings by key
     * @param $key (string or array)
     * @param null $lang
     * @return mixed
     */
    static function getByKey($key, $lang = null)
    {
        $lang = is_null($lang) ? config('app.locale_id') : $lang;

        $query = Settings::where(function ($q) use ($lang) {
            $q->orWhere('lang', $lang);
            $q->orWhere('lang', 0);
        });
        if (is_array($key))
            $query->whereIn('key', $key);
        else
            $query->where('key', $key);

        return $query->pluck('value', 'key');
    }
}
