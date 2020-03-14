<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $quali_data_id
 * @property int $participant_id
 * @property int $user_id
 * @property string $notes
 * @property QualiData $quali_data
 * @property Participant $participant
 * @property User|null $user
 * @property QualiRequirement[] $quali_requirements
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
    protected $fillable = ['notes'];
    protected $fillable_relations = ['participant', 'user'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function quali_data() {
        return $this->hasOne(QualiData::class);
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
     * Convenience method for geting the quali's name from the related quali_data.
     * @return string
     */
    public function getNameAttribute() {
        return $this->quali_data->name;
    }
}
