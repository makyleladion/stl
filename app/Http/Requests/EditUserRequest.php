<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
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
            'user_id' => 'required|integer',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'user_superior' => 'required|integer',
            'default_outlet' => 'nullable|integer',
            'is_read_only' => 'nullable|boolean',
        ];
    }
}
