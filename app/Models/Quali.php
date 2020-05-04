<?php

namespace App\Models;

use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $quali_data_id
 * @property int $participant_id
 * @property int $user_id
 * @property QualiData $quali_data
 * @property Participant $participant
 * @property User|null $user
 * @property QualiRequirement[] $quali_requirements
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
    protected $with = ['quali_data'];

    /**
     * @var array
     */
    protected $fillable = ['notes', 'participant_id'];
    protected $fillable_relations = ['participant', 'user', 'notes'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quali_data() {
        return $this->belongsTo(QualiData::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function course() {
        return $this->hasOneThrough(Course::class, QualiData::class);
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requirements() {
        return $this->hasMany(QualiRequirement::class);
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
    public function notes() {
        return $this->hasMany(QualiNote::class)->orderBy('order');
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
        return $this->notes->map(function (QualiNote $note) {
            return [
                'type' => 'text',
                'id' => $note->id,
                'order' => $note->order,
                'content' => $note->notes
            ];
        })->concat($this->observations->map(function (Observation $observation) {
            return [
                'type' => 'observation',
                'id' => $observation->pivot->id,
                'quali_id' => $observation->pivot->quali_id,
                'order' => $observation->pivot->order,
                'content' => $observation->content,
                'block' => $observation->block->name,
                'date' => $observation->block->block_date->formatLocalized('%A %d.%m.%Y'),
            ];
        }))->concat($this->requirements->map(function (QualiRequirement $requirement) {
            return [
                'type' => 'requirement',
                'id' => $requirement->id,
                'order' => $requirement->order,
                'content' => $requirement->requirement->content,
                'passed' => $requirement->passed,
                'contents' => $requirement->contents,
            ];
        }))->sortBy('order')->values();
    }
}
