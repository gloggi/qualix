<?php

namespace App\Http\Requests;

use App\Models\EvaluationGridRowTemplate;
use Illuminate\Support\Facades\Lang;

class EvaluationGridTemplateRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
            'requirements' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse|maxEntries:40',
            'blocks' => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'row_templates' => 'required',
            'row_templates.*.criterion' => 'required|max:65535',
            'row_templates.*.control_type' => 'required|in:' . implode(',', EvaluationGridRowTemplate::CONTROL_TYPES),
            'row_templates.*.control_config' => 'required|json' /*'required|json|validControlConfig'*/,
        ];
    }

    public function attributes() {
        $rowTemplateAttributes = collect(Lang::get('t.models.evaluation_grid_row_template'))->mapWithKeys(function ($item, $key) {
            return ["row_templates.*.$key" => $item];
        })->all();
        return array_merge(Lang::get('t.models.evaluation_grid_template'), $rowTemplateAttributes);
    }
}
