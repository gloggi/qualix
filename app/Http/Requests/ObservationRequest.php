<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class ObservationRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'participants' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'content' => 'required|max:1023',
            'impression' => 'required|in:0,1,2',
            'block' => 'required|regex:/^\d+$/|existsInCourse',
            'requirements' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'categories' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.observation');
    }
}
