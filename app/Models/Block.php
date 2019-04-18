<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * @property int $id
 * @property int $kurs_id
 * @property string $blockname
 * @property int $tagesnummer
 * @property int $blocknummer
 * @property string $full_block_number
 * @property CarbonInterface $datum
 * @property Kurs $kurs
 * @property Beobachtung[] $beobachtungen
 * @property MA[] $mas
 */
class Block extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'block';

    /**
     * @var array
     */
    protected $fillable = ['kurs_id', 'blockname', 'datum', 'full_block_number'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = ['datum' => 'date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kurs() {
        return $this->belongsTo('App\Models\Kurs', 'kurs_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function beobachtungen() {
        return $this->hasMany('App\Models\Beobachtung');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mas() {
        return $this->belongsToMany('App\Models\MA', 'block_ma', 'block_id', 'ma_id');
    }

    /**
     * Set the tagesnummer attribute by string or int.
     *
     * @param string|int $value
     */
    public function setTagesnummerAttribute($value) {
        $this->attributes['tagesnummer'] = ($value === null ? null : (int)$value);
    }

    /**
     * Set the blocknummer attribute by string or int.
     *
     * @param string|int $value
     */
    public function setBlocknummerAttribute($value) {
        $this->attributes['blocknummer'] = ($value === null ? null : (int)$value);
    }

    /**
     * Get the block date attribute in a localized format.
     *
     * @return CarbonInterface
     */
    public function getDatumAttribute() {
        return Carbon::parse($this->attributes['datum']);
    }

    /**
     * Set the block date attribute by a string date description.
     *
     * @param string $value
     */
    public function setDatumAttribute($value) {
        $this->attributes['datum'] = Carbon::parse($value);
    }

    /**
     * Get the full block number, combined from the tagesnummer and blocknummer attributes, if available.
     *
     * @return string|null
     */
    public function getFullBlockNumberAttribute() {
        if ($this->tagesnummer == null || $this->blocknummer == null) {
            return null;
        }
        return $this->tagesnummer . '.' . $this->blocknummer;
    }

    /**
     * Set the full block number, consisting of the tagesnummer and blocknummer separated by a period.
     *
     * @param string|null $value
     * @return void
     */
    public function setFullBlockNumberAttribute($value) {
        [$this->tagesnummer, $this->blocknummer] = ($value === null ? [null, null] : explode('.', $value, 2));
    }
}
