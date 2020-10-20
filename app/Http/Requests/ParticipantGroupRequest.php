<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class ParticipantGroupRequest extends FormRequest {

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
