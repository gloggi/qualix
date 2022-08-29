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
        'address-book',
        'address-card',
        'arrows-to-eye',
        'award',
        'ban',
        'bandage',
        'battery-full',
        'battery-half',
        'battery-quarter',
        'battery-three-quarters',
        'bell',
        'binoculars',
        'bolt',
        'bolt-lightning',
        'book',
        'bullhorn',
        'calendar-check',
        'calendar-day',
        'camera',
        'check-double',
        'check-to-slot',
        'child',
        'circle',
        'circle-check',
        'circle-down',
        'circle-exclamation',
        'circle-left',
        'circle-minus',
        'circle-plus',
        'circle-question',
        'circle-right',
        'circle-up',
        'circle-user',
        'circle-xmark',
        'clipboard',
        'clipboard-check',
        'clipboard-list',
        'clipboard-question',
        'clone',
        'cloud',
        'cloud-rain',
        'cloud-sun',
        'cloud-sun-rain',
        'comment',
        'comment-slash',
        'comments',
        'envelope-circle-check',
        'eye',
        'face-angry',
        'face-frown',
        'face-smile',
        'file-circle-check',
        'file-circle-exclamation',
        'file-circle-minus',
        'file-circle-plus',
        'file-circle-question',
        'file-circle-xmark',
        'file-lines',
        'hand-holding',
        'hand-holding-heart',
        'hand-holding-medical',
        'handshake-angle',
        'heart',
        'house',
        'house-laptop',
        'life-ring',
        'magnifying-glass',
        'medal',
        'message',
        'people-arrows-left-right',
        'person',
        'person-hiking',
        'print',
        'puzzle-piece',
        'rotate-right',
        'scale-balanced',
        'signs-post',
        'spell-check',
        'spinner',
        'square',
        'square-check',
        'square-minus',
        'square-plus',
        'star',
        'star-half-alt',
        'sun',
        'thumbs-down',
        'thumbs-up',
        'tower-observation',
        'triangle-exclamation',
        'user',
        'user-check',
        'user-clock',
        'user-gear',
        'user-graduate',
        'user-group',
        'user-lock',
        'user-minus',
        'user-pen',
        'user-plus',
        'user-shield',
        'user-xmark',
        'users',
        'users-gear',
        'virus-covid',
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
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
