<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $name
 * @property int $course_id
 * @property Course $course
 * @property Feedback[] $feedbacks
 */
class FeedbackData extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedback_datas';

    /**
     * @var array
     */
    protected $fillable = ['name', 'course_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course() {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedbacks() {
        return $this->hasMany(Feedback::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function participants() {
        return $this->hasManyThrough(Participant::class, Feedback::class, 'feedback_data_id', 'id', 'id', 'participant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function feedback_requirements() {
        return $this->hasManyThrough(FeedbackRequirement::class, Feedback::class, 'feedback_data_id', 'feedback_id', 'id', 'id');
    }
}
