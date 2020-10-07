<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property Course $course
 * @property Observation[] $observations
 * @property int $num_observations
 */
class Category extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'name'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['num_observations'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function observations()
    {
        return $this->belongsToMany('App\Models\Observation', 'observations_categories', 'category_id');
    }

    /**
     * Get the number of observations connected to this category.
     *
     * @return integer
     */
    public function getNumObservationsAttribute() {
        return $this->observations()->count();
    }
}
