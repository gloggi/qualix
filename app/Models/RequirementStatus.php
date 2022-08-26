<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property string $color
 * @property string $icon
 * @property Course $course
 */
class RequirementStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requirement_statuses';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'name', 'color', 'icon'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
}
