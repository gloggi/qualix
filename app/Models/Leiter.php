<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $kurs_id
 * @property int $user_id
 * @property Kurs $kurs
 * @property User $user
 */
class Leiter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leiter';

    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kurs()
    {
        return $this->belongsTo('App\Models\Kurs', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
