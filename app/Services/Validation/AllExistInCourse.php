<?php

namespace App\Services\Validation;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class AllExistInCourse extends ExistsInCourse {

    protected $delimiter;

    public function __construct(Route $route, $delimiter = ',') {
        parent::__construct($route);
        $this->delimiter = $delimiter;
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

        [$connection, $table, $column] = $this->getRelationTableAndColumn($attribute, $parameters, $validator);

        $criteria = array_filter(explode($this->delimiter, $value));

        return count($criteria) === DB::connection($connection)->table($table)
                ->whereIn($column, $criteria)
                ->where('course_id', $this->course->id)
                ->distinct()
                ->count($column);
    }

}
