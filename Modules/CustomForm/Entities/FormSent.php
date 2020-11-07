<?php

namespace Modules\CustomForm\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\CustomForm\Entities\FormSent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent query()
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $form_id
 * @property string $form_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent whereFormData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormSent whereUpdatedAt($value)
 */
class FormSent extends Model
{
    protected $fillable = ['form_id', 'form_data'];

    protected $casts = [
        'form_data' => 'array'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
