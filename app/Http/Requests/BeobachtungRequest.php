<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeobachtungRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tn_ids' => 'required|regex:/^\d+(,\d+)*$/',
            'kommentar' => 'required',
            'bewertung' => 'required',
            'block_id' => 'required',
            'ma_ids' => 'regex:/^\d+(,\d+)*$/|nullable',
            'qk_ids' => 'regex:/^\d+(,\d+)*$/|nullable',
        ];
    }
}
