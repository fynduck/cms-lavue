<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageValidate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return  [
            'items.' . config('app.fallback_locale_id') . '.title' => 'required',
            'items.' . config('app.fallback_locale_id') . '.url'   => 'required|sometimes|unique:page_trans,url,' . $this->route('page') . ',page_id,lang,' . config('app.locale_id'),
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
