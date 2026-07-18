<?php

namespace App\Models;

/**
 * @property int $id
 * @property int participant_id
 * @property int $block_id
 * @property int $impression
 * @property string $content
 * @property Block $block
 * @property Participant $participant
 * @property User[] $users
 * @property Requirement[] $requirements
 * @property Category[] $categories
 */
class Observation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'observations';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['block', 'users', 'participants', 'requirements', 'categories'];

    /**
     * @var array
     */
    protected $fillable = ['impression', 'content'];
    protected $fillable_relations = ['block'];

    /**
     * @var number
     */
    const CHAR_LIMIT = 1023;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block()
    {
        return $this->belongsTo('App\Models\Block');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany('App\Models\Participant', 'observations_participants');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'observations_users')->withPivot('order')->orderBy('observations_users.order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requirements()
    {
        return $this->belongsToMany('App\Models\Requirement', 'observations_requirements', 'observation_id', 'requirement_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'observations_categories', 'observation_id', 'category_id');
    }
}
