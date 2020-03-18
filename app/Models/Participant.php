<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $course_id
 * @property string $scout_name
 * @property string $group
 * @property string $image_url
 * @property Observation[] $positive
 * @property Observation[] $neutral
 * @property Observation[] $negative
 * @property Course $course
 * @property Observation[] $observations
 */
class Participant extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'participants';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'scout_name', 'group', 'image_url'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function observations() {
        return $this->belongsToMany(Observation::class, 'observations_participants');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualis() {
        return $this->hasMany(Quali::class);
    }

    public function getPositiveAttribute() {
        return $this->observations()->where('impression', '=', '2');
    }

    public function getNeutralAttribute() {
        return $this->observations()->where('impression', '=', '1');
    }

    public function getNegativeAttribute() {
        return $this->observations()->where('impression', '=', '0');
    }
}
