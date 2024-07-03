<?php

namespace App\Services\Validation;

use App\Models\Course;
use App\Models\Participant;
use App\Services\TiptapFormatter;
use Illuminate\Routing\Route;
use Illuminate\Validation\Validator;

class ValidControlConfig {

    /** @var Course $course */
    protected $course;

    public function __construct(Route $route) {
        $this->course = $route->parameter('course');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param mixed $value
     * @param $parameters
     * @param $validator Validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator) {
        if (!is_array($value)) return false;

        // TODO validate correctly using a suitable business logic class here
        //return true;
        throw new \Exception('Validating control config is not yet implemented');
    }

}
