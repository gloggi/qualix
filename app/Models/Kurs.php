<?php

namespace App\Models;

/**
 * @property int $id
 * @property string $name
 * @property string $kursnummer
 * @property Block[] $bloecke
 * @property Einladung[] $einladungen
 * @property User[] $users
 * @property MA[] $mas
 * @property QK[] $qks
 * @property Tn[] $tns
 */
class Kurs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'kursnummer'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bloecke()
    {
        return $this->hasMany('App\Models\Block', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function einladungen()
    {
        return $this->hasMany('App\Models\Einladung', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'leiter', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mas()
    {
        return $this->hasMany('App\Models\MA', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qks()
    {
        return $this->hasMany('App\Models\QK', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tns()
    {
        return $this->hasMany('App\Models\Tn', 'kurs_id');
    }
}
