<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int $kurs_id
 * @property string $username
 * @property string $abteilung
 * @property string $password
 * @property string $email
 * @property string $salt
 * @property string $bild_url
 * @property Beobachtung[] $beobachtungen
 * @property Kurs[] $kurse
 * @property LoginAttempt[] $loginAttempts
 * @property RecoveryAttempt[] $recoveryAttempts
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword;

    /**
     * @var array
     */
    protected $fillable = ['name', 'abteilung', 'password', 'email', 'bild_url'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['email_verified_at' => 'datetime'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function beobachtungen()
    {
        return $this->hasMany('App\Models\Beobachtung');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function kurse()
    {
        return $this->belongsToMany('App\Models\Kurs', 'leiter', null, 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginAttempts()
    {
        return $this->hasMany('App\Models\LoginAttempt');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recoveryAttempts()
    {
        return $this->hasMany('App\Models\RecoveryAttempt');
    }
}
