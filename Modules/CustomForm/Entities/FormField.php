<?php

namespace Modules\CustomForm\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\CustomForm\Entities\FormField
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $form_id
 * @property string $type
 * @property string|null $block_class
 * @property string $name
 * @property string|null $label
 * @property string|null $placeholder
 * @property string|null $field_class
 * @property string|null $field_id
 * @property string|null $validate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereBlockClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereFieldClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FormField whereValidate($value)
 * @property int|null $priority
 * @method static \Illuminate\Database\Eloquent\Builder|FormField wherePriority($value)
 */
class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'type',
        'block_class',
        'name',
        'label',
        'placeholder',
        'field_class',
        'field_id',
        'validate',
        'priority'
    ];

    public function getOptions()
    {
        return $this->hasMany(FieldOption::class, 'field_id');
    }

    public static function types()
    {
        return [
            'text'     => 'CustomForm.text',
            'number'   => 'CustomForm.number',
            'tel'      => 'CustomForm.telephone',
            'email'    => 'CustomForm.email',
            'checkbox' => 'CustomForm.checkbox',
            'radio'    => 'CustomForm.radio',
            'range'    => 'CustomForm.range',
            'file'     => 'CustomForm.file',
            'select'   => 'CustomForm.select',
            'textarea' => 'CustomForm.textarea'
        ];
    }

    public static function validations()
    {
        return [
            [
                'title' => 'CustomForm.required',
                'value' => 'required'
            ],
            [
                'title' => 'CustomForm.number',
                'value' => 'numeric'
            ],
            [
                'title' => 'CustomForm.email',
                'value' => 'email'
            ],
            [
                'title' => 'CustomForm.telephone',
                'value' => '(\+?\d[- .]*){7,13}'
            ],
            [
                'title' => 'CustomForm.file',
                'value' => 'file'
            ],
            [
                'title' => 'CustomForm.accepted',
                'value' => 'accepted'
            ]
        ];
    }
}
