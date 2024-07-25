<?php

namespace App\Http\Requests;

use App\Models\Observation;
use Illuminate\Support\Facades\Lang;

class EvaluationGridRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'participants' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'block' => 'required|regex:/^\d+$/|existsInCourse',
            'rows.*.value' => 'nullable|json',
            'rows.*.notes' => 'nullable|max:'.Observation::CHAR_LIMIT,
        ];
    }

    public function attributes() {
        $rowAttributes = collect(Lang::get('t.models.evaluation_grid_row'))->mapWithKeys(function ($item, $key) {
            return ["rows.*.$key" => $item];
        })->all();
        return array_merge(Lang::get('t.models.evaluation_grid'), $rowAttributes);
    }
}
