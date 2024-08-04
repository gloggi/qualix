<?php

namespace App\Http\Requests;

class EvaluationGridTemplateUpdateRequest extends EvaluationGridTemplateRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge(parent::rules(),
            // Existence of these ids in the evaluation grid template is implicitly ensured in the controller.
            // In other words, ids which do not exist in the evaluation grid template are simply ignored.
            [ 'row_templates.*.id' => 'nullable|numeric' ]
        );
    }
}
