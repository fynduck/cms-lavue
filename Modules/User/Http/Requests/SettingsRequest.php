<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'   => ['required', 'string', Rule::unique('users')->ignore(auth()->user())],
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user())],
            'country_id' => ['required'],
            'city_id'    => ['required'],
            'day'        => ['required'],
            'month'      => ['required'],
            'year'       => ['required'],
            'phone'      => ['required', 'numeric'],
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

    public function attributes()
    {
        return [
            'username'   => trans('auth.username'),
            'name'       => trans('auth.name'),
            'email'      => trans('auth.email'),
            'country_id' => trans('auth.your_country'),
            'city_id'    => trans('auth.your_city'),
            'day'        => trans('auth.day'),
            'month'      => trans('auth.month'),
            'year'       => trans('auth.year'),
            'phone'      => trans('auth.number_phone')
        ];
    }
}
