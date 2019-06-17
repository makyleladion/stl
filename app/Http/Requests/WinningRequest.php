<?php

namespace App\Http\Requests;

use App\System\Games\GamesFactory;
use Illuminate\Foundation\Http\FormRequest;

class WinningRequest extends FormRequest
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
        $rules = [
            'winning-timeslot' => 'date_format:H:i:s',
        ];

        foreach (GamesFactory::getGameNames() as $gameName) {
            $func = $gameName . 'Rules';
            $rules = array_merge($rules, $this->$func());
        }

        return $rules;
    }


    /**
     * Set rules for winning swertres.
     *
     * @return array
     */
    private function swertresRules() {
        return [
            'swertres-result1' => 'nullable|digits:3',
        ];
    }
    
    /**
     * Set rules for winning swertres STL.
     *
     * @return array
     */
    private function swertres_stlRules() {
        return [
            'swertres_stl-result1' => 'nullable|digits:3',
        ];
    }
    
    /**
     * Set rules for winning swertres STL Local.
     *
     * @return array
     */
    private function swertres_stl_localRules() {
        return [
            'swertres_stl_local-result1' => 'nullable|digits:3',
        ];
    }
    
    /**
     * Set rules for winning pares.
     *
     * @return array
     */
    private function paresRules() {
        return [
            'pares-result1' => 'nullable|digits_between:1,2|numeric|min:1|max:40',
            'pares-result2' => 'nullable|digits_between:1,2|numeric|min:1|max:40',
        ];
    }
}
