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

    public function qualis() {
        return $this->hasMany(Quali::class);
    }
}
