<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $course_id
 * @property string $email
 * @property string $token
 * @property Course $course
 */
class Invitation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitations';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'email', 'token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
}
