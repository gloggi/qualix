<?php

namespace App\Models;



class ObservationOrder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'observation_orders';


    /**
     * @var array
     */
    protected $fillable = ['order_name, course_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block()
    {
        return $this->belongsTo('App\Models\Block', 'fk_block_observation_order_block');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function participants()
    {
        return $this->belongsTo('App\Models\Participant', 'fk_participant_observation_order_participant');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo('App\Models\User', 'fk_user_observation_order_user');
    }
}
