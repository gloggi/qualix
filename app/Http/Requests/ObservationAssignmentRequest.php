<?php

namespace App\Http\Requests;

use App\Models\Trainer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;


class ObservationAssignmentRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'order_name' => 'required|max:1023',
            'blocks' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'participants' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'users' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse:' . Trainer::class . ',user_id'
        ];
    }


    public function attributes() {
        return Lang::get('t.models.observation_assignment');
    }
}
