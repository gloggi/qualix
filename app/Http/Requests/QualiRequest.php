<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class QualiRequest extends FormRequest {
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
            'name' => 'required|max:255',
            'participants' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'requirements' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'quali_notes_template' => 'nullable|max:2047',
            'qualis' => 'nullable|array',
            'qualis.*.user' => 'nullable|regex:/^\d+$/|existsInCourse:trainers,user_id',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.quali');
    }
}
