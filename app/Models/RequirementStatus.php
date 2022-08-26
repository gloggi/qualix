<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property string $color
 * @property string $icon
 * @property int $num_feedback_requirements
 * @property Course $course
 * @property FeedbackRequirement[] $feedback_requirements
 */
class RequirementStatus extends Model
{
    public const COLORS = [
        'blue',
        'indigo',
        'purple',
        'pink',
        'red',
        'orange',
        'yellow',
        'green',
        'teal',
        'cyan',
        'white',
        'gray-100',
        'gray-200',
        'gray-300',
        'gray-400',
        'gray-500',
        'gray-600',
        'gray-700',
        'gray-800',
        'gray-900',
        'black',
    ];
    public const ICONS = [
        'binoculars',
        'check-circle',
        'times-circle',
        'address-book',
        'address-card',
        'angry',
        'arrow-alt-circle-left',
        'arrow-alt-circle-right',
        'arrow-alt-circle-down',
        'arrow-alt-circle-up',
        'award',
        'balance-scale',
        'ban',
        'band-aid',
        'battery-full',
        'battery-three-quarters',
        'battery-half',
        'battery-quarter',
        'bell',
        'bolt',
        'book',
        'bullhorn',
        'calendar-day',
        'calendar-check',
        'camera',
        'check-double',
        'check-square',
        'child',
        'circle',
        'clipboard',
        'clipboard-check',
        'clipboard-list',
        'clone',
        'cloud-rain',
        'cloud-sun',
        'cloud',
        'cloud-sun-rain',
        'comment',
        'comment-alt',
        'comment-slash',
        'comments',
        'exclamation-circle',
        'exclamation-triangle',
        'eye',
        'file-alt',
        'frown',
        'hand-holding',
        'hand-holding-heart',
        'hand-holding-medical',
        'hands-helping',
        'heart',
        'hiking',
        'home',
        'laptop-house',
        'life-ring',
        'male',
        'map-signs',
        'medal',
        'minus-circle',
        'minus-square',
        'people-arrows',
        'plus-circle',
        'plus-square',
        'print',
        'puzzle-piece',
        'question-circle',
        'redo-alt',
        'search',
        'smile',
        'spell-check',
        'spinner',
        'square',
        'star',
        'star-half-alt',
        'sun',
        'thumbs-down',
        'thumbs-up',
        'user',
        'user-check',
        'user-circle',
        'user-clock',
        'user-cog',
        'user-edit',
        'user-friends',
        'user-graduate',
        'user-lock',
        'user-minus',
        'user-plus',
        'user-shield',
        'user-times',
        'users',
        'users-cog',
        'vote-yea'
    ];

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['num_feedback_requirements'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedback_requirements()
    {
        return $this->hasMany(FeedbackRequirement::class);
    }

    /**
     * Get the number of feedback_requirements which use this requirement status.
     *
     * @return integer
     */
    public function getNumFeedbackRequirementsAttribute() {
        return $this->feedback_requirements()->count();
    }
}
