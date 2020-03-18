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
    protected $fillable = ['notes', 'participant_id'];
    protected $fillable_relations = ['participant', 'user'];

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
}
