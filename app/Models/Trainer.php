<?php

namespace App\Models;

/**
 * @property int $course_id
 * @property int $user_id
 * @property Course $course
 * @property User $user
 */
class Trainer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trainers';

    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
