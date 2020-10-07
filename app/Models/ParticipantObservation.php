<?php

namespace App\Models;

/**
 * @property Observation $observation
 * @property Participant $participant
 */
class ParticipantObservation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'observations_participants';

    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function observation()
    {
        return $this->belongsTo(Observation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
