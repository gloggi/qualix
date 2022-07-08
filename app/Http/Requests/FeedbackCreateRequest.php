<?php

namespace App\Http\Requests;

class FeedbackCreateRequest extends FeedbackUpdateRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge(parent::rules(),
            ['feedback_contents_template' => 'required|validFeedbackContentWithoutObservations']
        );
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function prepareForValidation() {
        parent::prepareForValidation();
        $this->merge(['feedback_contents_template' => json_decode($this->get('feedback_contents_template'), true)]);
    }
}
