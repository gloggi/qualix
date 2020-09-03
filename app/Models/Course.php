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
 * @property Collection $participants
 * @property boolean $archived
 */
class Course extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'course_number', 'archived'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blocks()
    {
        return $this->hasMany('App\Models\Block', 'course_id')->orderBy('block_date')->orderBy('day_number')->orderBy('block_number')->orderBy('name')->orderBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany('App\Models\Invitation', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'trainers', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requirements()
    {
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
    public function categories()
    {
        return $this->hasMany('App\Models\Category', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany('App\Models\Participant', 'course_id')->orderBy('scout_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function observations()
    {
        return $this->hasManyThrough(Observation::class, Block::class)->orderBy('blocks.block_date')->orderBy('blocks.day_number')->orderBy('blocks.block_number')->orderBy('blocks.name')->orderBy('blocks.id');
    }
}
