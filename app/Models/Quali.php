<?php

namespace App\Models;

use App\Services\TiptapFormatter;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $quali_data_id
 * @property int $participant_id
 * @property int $user_id
 * @property QualiData $quali_data
 * @property Participant $participant
 * @property User|null $user
 * @property Requirement[] $requirements
 * @property Observation[] $observations
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
    protected $with = ['quali_data', 'requirements', 'user'];

    /**
     * @var array
     */
    protected $fillable = ['notes', 'participant_id'];
    protected $fillable_relations = ['participant', 'user', 'notes'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name', 'contents'];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
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
    public function observations() {
        return $this->belongsToMany(Observation::class, 'quali_observations')->withPivot('order')->orderBy('quali_observations.order');
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

    public function getContentsAttribute() {
        return $this->getTiptapFormatter()->toTiptap();
    }

    public function setContentsAttribute(array $contents) {
        $this->getTiptapFormatter()->applyToQuali($contents);
    }

    public function getHighestOrderNumberAttribute() {
        return $this->getTiptapFormatter()->getHighestOrderNumber();
    }

    protected $tiptapFormatter = null;

    /**
     * @return TiptapFormatter
     */
    protected function getTiptapFormatter() {
        if (!$this->tiptapFormatter) $this->tiptapFormatter = new TiptapFormatter($this);
        return $this->tiptapFormatter;
    }
}
