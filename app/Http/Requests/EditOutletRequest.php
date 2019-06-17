<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditOutletRequest extends FormRequest
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
            'outlet_id' => 'required|integer',
            'outlet-name' => 'required',
            'address' => 'required',
            'is-affiliated' => 'boolean|nullable',
        ];
    }
}
