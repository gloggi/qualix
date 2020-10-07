<?php

namespace App\Http\Requests;

class QualiCreateRequest extends QualiUpdateRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge(parent::rules(),
            ['quali_contents_template' => 'required|validQualiContentWithoutObservations']
        );
    }
}
