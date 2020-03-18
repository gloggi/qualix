<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $name
 * @property int $course_id
 * @property Course $course
 * @property Quali[] $qualis
 */
class QualiData extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quali_datas';

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
    public function qualis() {
        return $this->hasMany(Quali::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function participants() {
        return $this->hasManyThrough(Participant::class, Quali::class, 'quali_data_id', 'id', 'id', 'participant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function quali_requirements() {
        return $this->hasManyThrough(QualiRequirement::class, Quali::class, 'quali_data_id', 'quali_id', 'id', 'id');
    }
}
