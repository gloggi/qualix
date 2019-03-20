<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property string $time
 * @property User $user
 */
class LoginAttempt extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'time'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
