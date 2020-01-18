<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Builder
 */
abstract class Model extends BaseModel
{
    /**
     * Attaches related records specified by an id list to the model, replacing all previously attached related entities.
     *
     * @param Course $course the active course
     * @param string $relation the name of the manyToMany relation
     * @param array $data validated data from a request
     * @param string $fieldName the field name from the request that contains the related record ids
     * @return Collection the related records that are attached to the observation
     * @throws ValidationException if some of the related records are not found in the course
     */
    public function attachRelatedRecords(Course $course, string $relation, array $data, string $fieldName) {
        try {
            $relatedRecords = $course->$relation()->findOrFail(array_filter(explode(',', $data[$fieldName])));
            $this->$relation()->detach();
            $this->$relation()->attach($relatedRecords);
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([ $fieldName => trans('validation.exists', ['attribute' => trans("t.models.observation.$relation") ]) ]);
        }
        return $relatedRecords;
    }
}
