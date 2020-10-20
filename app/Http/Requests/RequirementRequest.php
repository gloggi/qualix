<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class RequirementRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'content' => 'required|max:255',
            'mandatory' => 'boolean',
            'blocks' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.requirement');
    }
}
