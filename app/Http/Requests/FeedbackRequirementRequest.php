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
            'comment' => 'max:4000' // VARCHAR column maximum 16000 bytes = 4000 UTF-32 characters
        ];
    }

    public function attributes() {
        return Lang::get('t.models.feedback_requirement');
    }

    public function validated($key = null, $default = null) {
        $data = parent::validated($key, $default);
        if (is_null($data['comment'])) $data['comment'] = '';
        return $data;
    }
}
