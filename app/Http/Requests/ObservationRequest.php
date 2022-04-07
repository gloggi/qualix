<?php

namespace App\Http\Requests;

use App\Models\Observation;
use Illuminate\Support\Facades\Lang;

class ObservationRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'participants' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'content' => 'required|max:'.Observation::CHAR_LIMIT,
            'impression' => 'in:0,1,2',
            'block' => 'required|regex:/^\d+$/|existsInCourse',
            'requirements' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'categories' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.observation');
    }
}
