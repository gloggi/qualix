<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelFillableRelations\Eloquent\Concerns\HasFillableRelations;

/**
 * @mixin Builder
 */
abstract class Model extends BaseModel {
    use HasFillableRelations;

    protected $fillable_relations = [];

    /**
     * @param BelongsTo $relation
     * @param array|\Illuminate\Database\Eloquent\Model $attributes
     */
    public function fillBelongsToRelation(BelongsTo $relation, $attributes, $relationName) {
        $entity = $attributes;
        if (is_array($attributes)) {
            $entity = $relation->getRelated()
                ->where($attributes)->firstOrFail();
        }

        $relation->associate($entity);
    }

    /**
     * Returns the name of the database table that is used to store this model.
     *
     * @return string table name
     */
    public static function tableName() {
        return with(new static)->getTable();
    }
}
