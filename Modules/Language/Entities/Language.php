<?php

namespace Modules\Language\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Language\Traits\LanguageTrait;

/**
 * Modules\Language\Entities\Language
 *
 * @property int $id
 * @property string $country_iso
 * @property string $slug
 * @property string $name
 * @property string|null $image
 * @property int $active
 * @property int $default
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language filter(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereCountryIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Language\Entities\Language whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Language wherePriority($value)
 */
class Language extends Model
{
    use LanguageTrait;

    const FOLDER_IMG = 'languages';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'country_iso',
        'active',
        'default',
        'priority'
    ];
}
