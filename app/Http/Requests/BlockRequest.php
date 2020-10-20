<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class BlockRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
            'full_block_number' => 'regex:/^\d+\.\d+$/|nullable',
            'block_date' => 'date|required',
            'requirements' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.block');
    }
}
