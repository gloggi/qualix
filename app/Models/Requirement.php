<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $course_id
 * @property string $content
 * @property bool $mandatory
 * @property Course $course
 * @property Observation[] $observations
 * @property Block[] $blocks
 * @property RequirementDetail[] $requirementDetails
 * @property FeedbackRequirement[] $feedback_requirements
 * @property int $num_observations
 */
class Requirement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requirements';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'content', 'mandatory'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['mandatory' => 'bool'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['num_observations', 'num_feedback_datas'];

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
        return $this->belongsToMany('App\Models\Observation', 'observations_requirements', 'requirement_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blocks()
    {
        return $this->belongsToMany('App\Models\Block', 'blocks_requirements', 'requirement_id', 'block_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requirementDetails()
    {
        return $this->hasMany('App\Models\RequirementDetail');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedback_requirements()
    {
        return $this->hasMany(FeedbackRequirement::class);
    }

    /**
     * Get the number of observations connected to this category.
     *
     * @return integer
     */
    public function getNumObservationsAttribute() {
        return $this->observations()->count();
    }

    /**
     * Get the number of observations connected to this category.
     *
     * @return integer
     */
    public function getNumFeedbackDatasAttribute() {
        $requirementId = $this->id;
        return $this->course->feedback_datas()->whereHas('feedbacks.requirements', function (Builder $query) use($requirementId) {
            $query->where('requirements.id', $requirementId);
        })->count();
    }
}
