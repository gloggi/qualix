<?php

namespace App\Http\Requests;

class MultiParticipantGroupRequest extends ParticipantGroupRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return collect(parent::rules())->mapWithKeys(function ($item, $key) {
            return ["participantGroups.*.$key" => $item];
        })->all();
    }

    public function attributes() {
        return collect(parent::attributes())->mapWithKeys(function ($item, $key) {
            return ["participantGroups.*.$key" => $item];
        })->all();
    }
}
