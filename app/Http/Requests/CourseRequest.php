<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;

class CourseRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
            'course_number' => 'max:255',
            'observation_count_red_threshold' => 'required|integer|min:0',
            'observation_count_green_threshold' => 'required|integer|min:0|gte:observation_count_red_threshold',
        ];
    }

    public function attributes() {
        return Lang::get('t.models.course');
    }
}
