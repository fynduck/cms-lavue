<?php

namespace Modules\CustomForm\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\CustomForm\Entities\FormShow
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $form_id
 * @property int $item_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormShow whereUpdatedAt($value)
 */
class FormShow extends Model
{
    protected $fillable = ['form_id', 'item_id', 'type'];

    public function getForm()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
