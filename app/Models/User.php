<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Carbon\CarbonInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $kurs_id
 * @property string $name
 * @property string $abteilung
 * @property string $password
 * @property string $email
 * @property string $salt
 * @property string $bild_url
 * @property Beobachtung[] $beobachtungen
 * @property Kurs[] $kurse
 * @property Kurs $last_accessed_kurs
 * @property LoginAttempt[] $loginAttempts
 * @property RecoveryAttempt[] $recoveryAttempts
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, MustVerifyEmailContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

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
        return $this->belongsToMany('App\Models\Kurs', 'leiter', null, 'kurs_id')->withPivot('last_accessed')->orderByDesc('leiter.last_accessed');
    }

    /**
     * Get the currently viewed kurs of the user.
     *
     * @return Kurs
     */
    public function getLastAccessedKursAttribute() {
        return $this->kurse()->firstOrFail();
    }

    /**
     * Get the last date value that the user entered into a block's date field in the given Kurs, or today if not available.
     *
     * @param Kurs $kurs
     * @return CarbonInterface
     */
    public function getLastUsedBlockDate(Kurs $kurs) {
        $date = Carbon::parse($this->kurse()->withPivot('last_used_block_date')->findOrFail($kurs->id)->pivot->last_used_block_date);
        $carbon = $date ?? Carbon::today();
        return $carbon;
    }

    /**
     * Set the last date value that the user entered into a block's date field.
     *
     * @param string $value
     */
    public function setLastUsedBlockDate($value, Kurs $kurs) {
        $this->kurse()->updateExistingPivot($kurs->id, ['last_used_block_date' => Carbon::parse($value)]);
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

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
