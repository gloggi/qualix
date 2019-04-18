<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $kurs_id
 * @property string $pfadiname
 * @property string $abteilung
 * @property string $bild_url
 * @property Beobachtung[] $positive
 * @property Beobachtung[] $neutral
 * @property Beobachtung[] $negative
 * @property Kurs $kurs
 * @property Beobachtung[] $beobachtungen
 */
class TN extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tn';

    /**
     * @var array
     */
    protected $fillable = ['kurs_id', 'pfadiname', 'abteilung', 'bild_url'];

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
        return $this->hasMany('App\Models\Beobachtung', 'tn_id');
    }

    public function getPositiveAttribute() {
        return $this->beobachtungen()->where('bewertung', '=', '2');
    }

    public function getNeutralAttribute() {
        return $this->beobachtungen()->where('bewertung', '=', '1');
    }

    public function getNegativeAttribute() {
        return $this->beobachtungen()->where('bewertung', '=', '0');
    }
}
