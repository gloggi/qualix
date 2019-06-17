<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $requirement_id
 * @property string $content
 * @property int $mandatory
 * @property Requirement $requirement
 */
class RequirementDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requirement_details';

    /**
     * @var array
     */
    protected $fillable = ['requirement_id', 'content', 'mandatory'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requirement()
    {
        return $this->belongsTo('App\Models\Requirement');
    }
}
