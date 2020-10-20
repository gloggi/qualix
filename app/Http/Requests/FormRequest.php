<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;
use Illuminate\Validation\ValidationRuleParser;

class FormRequest extends LaravelFormRequest {

    /**
     * Set all unused parameters to null, to avoid problems later
     */
    protected function prepareForValidation() {
        parent::prepareForValidation();
        $rules = $this->container->call([$this, 'rules']);
        $rules = collect((new ValidationRuleParser($this->all()))->explode($rules)->rules);
        $rules->each(function($ruleList, $key) {
            if (collect($ruleList)->some(function($rule) { return $rule === 'nullable'; })) {
                $this->merge([$key => $this->get($key, null)]);
            }
        });
    }

}
