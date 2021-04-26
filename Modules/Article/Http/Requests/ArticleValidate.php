<?php

namespace Modules\Article\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleValidate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'items.' . config('app.fallback_locale_id') . '.title' => [
                'required',
                'unique:article_trans,title,' . $this->route('article') . ',article_id'
            ],
            'items.' . config('app.fallback_locale_id') . '.slug'  => [
                'required',
                'unique:article_trans,slug,' . $this->route('article') . ',article_id'
            ],
            'type'                                                 => 'required',
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
