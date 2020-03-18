<?php

namespace App\Services\Validation;

use App\Models\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class ExistsInCourse {

    protected $course;
    protected $modelNamespace;

    public function __construct(Route $route) {
        $this->course = $route->parameter('course');
        $this->modelNamespace = with(new \ReflectionClass(Model::class))->getNamespaceName();
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

        return 1 === DB::connection($connection)->table($table)
                ->where($column, $value)
                ->where('course_id', $this->course->id)
                ->count($column);
    }

    /**
     * @param $attribute
     * @param $parameters
     * @param $validator Validator
     * @return array
     */
    protected function getRelationTableAndColumn($attribute, $parameters, $validator) {

        if (count($parameters) > 1) {
            $column = $parameters[1];
        } else {
            $column = 'id';
        }

        if (count($parameters) > 0) {
            [$connection, $table] = $validator->parseTable($parameters[0]);
        } else {
            $connection = null;
            $model = $this->modelNamespace . '\\' . $this->guessModelName($attribute);
            $table = call_user_func($model . '::tableName');
        }

        return [$connection, $table, $column];
    }

    protected function guessModelName($attribute) {
        // In case the attribute name is a nested attribute like 'related.*.some_other_model',
        // use just the last portion 'some_other_model'
        $segments = explode('.', $attribute);
        $attribute = end($segments);

        return Str::singular(Str::studly($attribute));
    }

}
