<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use App\Models\Trainer;


class ObservationOrderRequest extends FormRequest {
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
            'block' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'participants' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'user' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse:' . Trainer::class . ',user_id'        ];
    }


    public function attributes() {
        return Lang::get('t.models.observation_order');
    }
}
