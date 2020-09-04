<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class ParticipantGroupRequest extends FormRequest {
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
            'group_name' => 'required|max:1023'
        ];
    }


    public function attributes() {
        return Lang::get('t.models.participant_group');
    }
}
