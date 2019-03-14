<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $kurs_id
 * @property string $quali_kategorie
 * @property Kurs $kurs
 * @property Beobachtung[] $beobachtungen
 */
class QK extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qk';

    /**
     * @var array
     */
    protected $fillable = ['kurs_id', 'quali_kategorie'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kurs()
    {
        return $this->belongsTo('App\Models\Kurs', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function beobachtungen()
    {
        return $this->belongsToMany('App\Models\Beobachtung');
    }
}
