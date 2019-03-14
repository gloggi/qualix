<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $kurs_id
 * @property int $tagesnummer
 * @property string $blockname
 * @property string $datum
 * @property int $blocknummer
 * @property Kurs $kurs
 * @property Beobachtung[] $beobachtungen
 * @property MA[] $mas
 */
class Block extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'block';

    /**
     * @var array
     */
    protected $fillable = ['kurs_id', 'tagesnummer', 'blockname', 'datum', 'blocknummer'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kurs()
    {
        return $this->belongsTo('App\Models\Kurs', 'kurs_id');
    }

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
    public function mas()
    {
        return $this->belongsToMany('App\Models\MA');
    }
}
