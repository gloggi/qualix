<?php

namespace App\Models;

use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $course_number
 * @property Block[] $blocks
 * @property Invitation[] $invitations
 * @property User[] $users
 * @property Requirement[] $requirements
 * @property Category[] $categories
 * @property Observation[] $observations
 * @property Collection $participants
 * @property QualiData[] $quali_datas
 * @property Quali[] $qualis
 * @property boolean $archived
 * @property array $qualis_using_observations
 */
class Course extends Model {
    /**
     * @var array
     */
    protected $fillable = ['name', 'course_number', 'archived'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blocks() {
        return $this->hasMany('App\Models\Block', 'course_id')->orderBy('block_date')->orderBy('day_number')->orderBy('block_number')->orderBy('name')->orderBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations() {
        return $this->hasMany('App\Models\Invitation', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany('App\Models\User', 'trainers', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requirements() {
        return $this->hasMany('App\Models\Requirement', 'course_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participantGroups()
    {
        return $this->hasMany('App\Models\ParticipantGroup', 'course_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observationOrders()
    {
        return $this->hasMany('App\Models\ObservationOrder', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories() {
        return $this->hasMany('App\Models\Category', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants() {
        return $this->hasMany('App\Models\Participant', 'course_id')->orderBy('scout_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function observations() {
        return $this->hasManyThrough(Observation::class, Block::class)->orderBy('blocks.block_date')->orderBy('blocks.day_number')->orderBy('blocks.block_number')->orderBy('blocks.name')->orderBy('blocks.id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quali_datas() {
        return $this->hasMany(QualiData::class)->orderBy('name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function qualis() {
        return $this->hasManyThrough(Quali::class, QualiData::class);
    }

    /**
     * Get the names of all Qualis that contain this observation.
     *
     * @return array
     */
    public function getQualisUsingObservationsAttribute() {
        return $this->qualis()
            ->select(['qualis.*', 'observations_participants.observation_id as observation_id'])
            ->distinct()
            ->join('quali_observations_participants', 'qualis.id', 'quali_observations_participants.quali_id')
            ->join('observations_participants', 'observations_participants.id', 'quali_observations_participants.participant_observation_id')
            ->get()
            ->mapToGroups(function(Quali $quali) {
                return [$quali->observation_id => $quali->display_name];
            })->all();
    }
}
