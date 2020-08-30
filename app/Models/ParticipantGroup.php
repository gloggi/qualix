<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany('App\Models\Participant');
    }
}
