<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $course_id
 * @property string $group_name
 * @property Participant[] $participants
 */
class ParticipantGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'participant_groups';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'group_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany('App\Models\Participant','participant_groups_participants');
    }
}
