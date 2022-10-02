<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class BlockGenerateRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
            'blocks_startdate' => 'date|required',
            'blocks_enddate' => 'date|required',
            'requirements' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.block');
    }
}
