<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOutletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->user()->is_admin) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'regex:/^[\pL\s\-]+$/u',
            'email' => 'required|email',
            'password' => 'min:8',
            'outlet-name' => 'required',
            'address' => 'required',
            'user-is-exist' => 'required|boolean',
            'is-affiliated' => 'boolean|nullable',
        ];
    }
}
