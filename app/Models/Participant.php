<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $course_id
 * @property string $scout_name
 * @property string $group
 * @property string $image_url
 * @property Observation[] $positive
 * @property Observation[] $neutral
 * @property Observation[] $negative
 * @property Course $course
 * @property Observation[] $observations
 * @property int $num_observations
 * @property string $image_path
 */
class Participant extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'participants';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'scout_name', 'group', 'image_url'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['num_observations', 'image_path', 'name_and_group'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function observations() {
        return $this->belongsToMany(Observation::class, 'observations_participants');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participant_observations() {
        return $this->hasMany(ParticipantObservation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualis() {
        return $this->hasMany(Quali::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observationOrders()
    {
        return $this->hasMany('App\Models\ObservationOrder');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participant_groups()
    {
        return $this->belongsToMany('App\Models\ParticipantGroup', 'participant_groups_participants');
    }

    public function getPositiveAttribute() {
        return $this->observations()->where('impression', '=', '2');
    }

    public function getNeutralAttribute() {
        return $this->observations()->where('impression', '=', '1');
    }

    public function getNegativeAttribute() {
        return $this->observations()->where('impression', '=', '0');
    }

    public function getNameAndGroupAttribute() {
        return $this->group ? $this->scout_name . ' (' . $this->group . ')' : $this->scout_name;
    }

    /**
     * Get the number of observations grouped by the users that created the observation.
     *
     * @return array
     */
    public function observationCountsByUser() {
        return [
            'id' => $this->id,
            'scout_name' => $this->scout_name,
            'course_id' => $this->course_id,
            'observation_counts_by_user' => $this->observations()
                ->select('user_id')
                ->selectRaw('count(*) as total')
                ->groupBy('user_id')
                ->pluck('total', 'user_id'),
        ];
    }

    /**
     * Get the number of observations connected to this category.
     *
     * @return integer
     */
    public function getNumObservationsAttribute() {
        return $this->observations()->count();
    }

    /**
     * Get the linkable relative URL to the participant image.
     *
     * @return integer
     */
    public function getImagePathAttribute() {
        return $this->image_url ? asset(Storage::url($this->image_url)) : null;
    }
}
