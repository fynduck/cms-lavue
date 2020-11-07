<?php

namespace Modules\CustomForm\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\CustomForm\Entities\FieldOption;
use Modules\CustomForm\Entities\Form;
use Modules\CustomForm\Entities\FormField;
use Modules\CustomForm\Entities\FormShow;

class CreateFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'form_name'              => 'required',
            'method'                 => 'required',
            'action'                 => 'required',
            'fields.*.type'          => 'required',
            'fields.*.name'          => 'required',
            'fields.*.options.*.title' => 'sometimes|required',
            'fields.*.options.*.value' => 'sometimes|required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
