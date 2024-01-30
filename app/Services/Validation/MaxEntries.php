<?php

namespace App\Services\Validation;

use Illuminate\Validation\Validator;

class MaxEntries {

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param string $value
     * @param $parameters
     * @param $validator Validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator) {
        return $parameters[0] >= count(explode(',', $value));
    }

}
