<?php

namespace App\Http\Requests;

use App\Models\Trainer;
use Illuminate\Support\Facades\Lang;


class ObservationAssignmentRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:1023',
            'blocks' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'participants' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'users' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse:' . Trainer::class . ',user_id'
        ];
    }


    public function attributes() {
        return Lang::get('t.models.observation_assignment');
    }
}
