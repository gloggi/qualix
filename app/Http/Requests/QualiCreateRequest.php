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

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function prepareForValidation() {
        parent::prepareForValidation();
        $this->merge(['quali_contents_template' => json_decode($this->get('quali_contents_template'), true)]);
    }
}
