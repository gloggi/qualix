<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $tn_id
 * @property int $block_id
 * @property int $user_id
 * @property int $bewertung
 * @property string $kommentar
 * @property Block $block
 * @property TN $tn
 * @property User $user
 * @property MA[] $mas
 * @property QK[] $qks
 */
class Beobachtung extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'beobachtung';

    /**
     * @var array
     */
    protected $fillable = ['tn_id', 'block_id', 'user_id', 'bewertung', 'kommentar'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block()
    {
        return $this->belongsTo('App\Models\Block');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tn()
    {
        return $this->belongsTo('App\Models\TN');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mas()
    {
        return $this->belongsToMany('App\Models\MA');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function qks()
    {
        return $this->belongsToMany('App\Models\QK');
    }
}
