<?php

namespace App\Models;

use App\Exceptions\RequirementsMismatchException;
use App\Services\TiptapFormatter;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $feedback_data_id
 * @property int $participant_id
 * @property FeedbackData $feedback_data
 * @property Participant $participant
 * @property User[] $users
 * @property Requirement[] $requirements
 * @property ParticipantObservation[] $participant_observations
 * @property FeedbackContentNode[] $contentNodes
 * @property Collection $contents
 */
class Feedback extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedbacks';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['feedback_data', 'requirements', 'users'];

    /**
     * @var array
     */
    protected $fillable = ['notes', 'participant_id'];
    protected $fillable_relations = ['participant', 'notes'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name'];

    protected static function booted() {
        static::creating(function ($feedback) {
            $feedback->collaborationKey = Str::random(32);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedback_data() {
        return $this->belongsTo(FeedbackData::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function participant() {
        return $this->belongsTo(Participant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(User::class, 'feedbacks_users');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function requirements() {
        return $this->hasManyThrough(Requirement::class, FeedbackRequirement::class, 'feedback_id', 'id', 'id', 'requirement_id')
            ->join('requirement_statuses', 'requirement_statuses.id', '=', 'feedback_requirements.requirement_status_id')
            ->select(['requirements.*', 'feedback_requirements.order as order', 'requirement_statuses.id as status_id'])
            ->orderBy('feedback_requirements.order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedback_requirements() {
        return $this->hasMany(FeedbackRequirement::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participant_observations() {
        return $this->belongsToMany(ParticipantObservation::class, 'feedback_observations_participants')->withPivot('order')->with('observation')->orderBy('feedback_observations_participants.order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentNodes() {
        return $this->hasMany(FeedbackContentNode::class)->orderBy('order');
    }

    /**
     * Convenience method for getting the feedback's name from the related feedback_data.
     * @return string
     */
    public function getNameAttribute() {
        return $this->feedback_data->name;
    }

    /**
     * Convenience method for setting the feedback's name in the related feedback_data.
     * @param $name
     * @return void
     */
    public function setNameAttribute($name) {
        $this->feedback_data->attributes['name'] = $name;
    }

    /**
     * Display name of the feedback including the participant's name.
     * @return string
     */
    public function getDisplayNameAttribute() {
        return trans('t.models.feedback.display_name', [ 'feedback_name' => $this->getNameAttribute(), 'participant_name' => $this->participant->scout_name ]);
    }

    public function getContentsAttribute() {
        return $this->getTiptapFormatter()->toTiptap();
    }

    /**
     * @param array $contents
     * @throws RequirementsMismatchException
     */
    public function setContentsAttribute(array $contents) {
        $tiptapFormatter = $this->getTiptapFormatter();
        $tiptapFormatter->applyToFeedback($contents);
    }

    public function appendRequirements(Collection $requirements) {
        $this->getTiptapFormatter()->appendRequirementsToFeedback($requirements);
    }

    protected $tiptapFormatter = null;

    /**
     * @return TiptapFormatter
     */
    protected function getTiptapFormatter() {
        return app()->makeWith(TiptapFormatter::class, ['feedback' => $this]);
    }
}
