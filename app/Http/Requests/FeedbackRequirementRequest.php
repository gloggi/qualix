<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class FeedbackRequirementRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'requirement_status' => 'required|existsInCourse',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.feedback_requirement');
    }
}
