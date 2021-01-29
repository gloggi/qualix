<?php

namespace App\Models;

use App\Exceptions\RequirementsMismatchException;
use App\Services\TiptapFormatter;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $quali_data_id
 * @property int $participant_id
 * @property QualiData $quali_data
 * @property Participant $participant
 * @property User[] $users
 * @property Requirement[] $requirements
 * @property ParticipantObservation[] $participant_observations
 * @property QualiContentNode[] $contentNodes
 * @property int $highest_order_number
 * @property Collection $contents
 */
class Quali extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qualis';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['quali_data', 'requirements', 'users'];

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
    protected $appends = ['name', 'contents', 'display_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quali_data() {
        return $this->belongsTo(QualiData::class);
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
        return $this->belongsToMany(User::class, 'qualis_users');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requirements() {
        return $this->belongsToMany(Requirement::class, 'quali_requirements')->withPivot('order', 'passed')->orderBy('quali_requirements.order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participant_observations() {
        return $this->belongsToMany(ParticipantObservation::class, 'quali_observations_participants')->withPivot('order')->with('observation')->orderBy('quali_observations_participants.order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentNodes() {
        return $this->hasMany(QualiContentNode::class)->orderBy('order');
    }

    /**
     * Convenience method for getting the quali's name from the related quali_data.
     * @return string
     */
    public function getNameAttribute() {
        return $this->quali_data->name;
    }

    /**
     * Convenience method for setting the quali's name in the related quali_data.
     * @param $name
     * @return void
     */
    public function setNameAttribute($name) {
        $this->quali_data->attributes['name'] = $name;
    }

    /**
     * Display name of the quali including the participant's name.
     * @return string
     */
    public function getDisplayNameAttribute() {
        return trans('t.models.quali.display_name', [ 'quali_name' => $this->getNameAttribute(), 'participant_name' => $this->participant->scout_name ]);
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
        $tiptapFormatter->applyToQuali($contents);
    }

    public function appendRequirements(Collection $requirements) {
        $this->getTiptapFormatter()->appendRequirementsToQuali($requirements);
    }

    protected $tiptapFormatter = null;

    /**
     * @return TiptapFormatter
     */
    protected function getTiptapFormatter() {
        return app()->makeWith(TiptapFormatter::class, ['quali' => $this]);
    }
}
