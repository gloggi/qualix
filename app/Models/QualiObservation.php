<?php

namespace App\Models;

/**
 * @property int $quali_requirement_id
 * @property int $observation_id
 * @property boolean|null $passed
 * @property string $notes
 * @property QualiRequirement $quali_requirement
 * @property Observation $observation
 */
class QualiObservation extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quali_observations';

    /**
     * @var array
     */
    protected $fillable = ['notes', 'observation_id'];
    protected $fillable_relations = ['quali_requirement', 'observation'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quali_requirement() {
        return $this->belongsTo(QualiRequirement::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function observation() {
        return $this->belongsTo(Observation::class);
    }
}
