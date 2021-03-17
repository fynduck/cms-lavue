<?php

namespace Modules\Banner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerValidate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'position'  => 'required',
            'image'     => 'required_without:old_image',
            'old_image' => 'required_without:image'
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
