<?php

namespace App\Http\Requests;

use App\Models\EvaluationGridRowTemplate;

class EvaluationGridTemplateRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|max:255',
            'requirements' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse|maxEntries:40',
            'blocks' => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
            'row_templates.*.criterion' => 'required|max:65535',
            'row_templates.*.control_type' => 'required|in:' . implode(',', EvaluationGridRowTemplate::CONTROL_TYPES),
            'row_templates.*.control_config' => 'required|json|validControlConfig',
        ];
    }
}
