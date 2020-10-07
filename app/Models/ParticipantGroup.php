<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $course_id
 * @property string $group_name
 * @property Participant[] $participants
 * @property string $participant_names
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['participant_names'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany('App\Models\Participant','participant_groups_participants');
    }

    /**
     * Get the number of observations connected to this category.
     *
     * @return integer
     */
    public function getParticipantNamesAttribute() {
        return $this->participants->map(function (Participant $participant){
            $scout_name = $participant->scout_name;
            $group = $participant->group;
            return $group ? "$scout_name ($group)" : $scout_name;
        })->implode(', ');
    }
}
