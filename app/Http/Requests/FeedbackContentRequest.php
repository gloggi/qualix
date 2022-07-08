<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class FeedbackContentRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'feedback_contents' => 'required|validFeedbackContent',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.feedback');
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function prepareForValidation() {
        parent::prepareForValidation();
        $this->merge(['feedback_contents' => json_decode($this->get('feedback_contents'), true)]);
    }
}
