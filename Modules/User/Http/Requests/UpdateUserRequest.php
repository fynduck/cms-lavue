<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string|max:100|unique:users,username,' . ($this->route('user') ?? ''),
            'name'     => 'required|max:255',
            'birthday' => 'sometimes|date',
            'email'    => 'required|email:strict,dns|max:255|unique:users,email,' . ($this->route('user') ?? ''),
            'group_id' => 'required|numeric'
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
