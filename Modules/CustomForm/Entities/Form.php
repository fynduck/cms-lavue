<?php

namespace Modules\CustomForm\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\CustomForm\Entities\Form
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form filter($request)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $form_name
 * @property string $action
 * @property string $method
 * @property int|null $file
 * @property string|null $form_class
 * @property string|null $form_id
 * @property string|null $send_emails
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereFormClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereFormName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereSendEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\Form whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\CustomForm\Entities\FormSent[] $completed
 * @property-read int|null $completed_count
 */
class Form extends Model
{
    protected $fillable = [
        'form_name',
        'action',
        'method',
        'file',
        'form_class',
        'form_id',
        'send_emails'
    ];

    public function scopeFilter($query, $request)
    {
        if ($request->get('q'))
            $query->where('form_name', 'LIKE', '%' . $request->get('q') . '%');
    }

    public function getFields()
    {
        return $this->hasMany(FormField::class, 'form_id');
    }

    public function getShow()
    {
        return $this->hasMany(FormShow::class, 'form_id');
    }

    public function completed()
    {
        return $this->hasMany(FormSent::class, 'form_id');
    }
}
