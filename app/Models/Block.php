<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property int $day_number
 * @property int $block_number
 * @property string $full_block_number
 * @property string $blockname_and_number
 * @property CarbonInterface $block_date
 * @property Course $course
 * @property Observation[] $observations
 * @property Collection $requirements
 */
class Block extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blocks';

    /**
     * @var array
     */
    protected $fillable = ['course_id', 'name', 'block_date', 'full_block_number'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = ['block_date' => 'date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course() {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observations() {
        return $this->hasMany('App\Models\Observation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requirements() {
        return $this->belongsToMany('App\Models\Requirement', 'blocks_requirements', 'block_id', 'requirement_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mandatory_requirements() {
        return $this->requirements()->where('mandatory', '!=', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function non_mandatory_requirements() {
        return $this->requirements()->where('mandatory', '==', 0);
    }

    /**
     * Set the day_number attribute by string or int.
     *
     * @param string|int $value
     */
    public function setDayNumberAttribute($value) {
        $this->attributes['day_number'] = ($value === null ? null : (int)$value);
    }

    /**
     * Set the block_number attribute by string or int.
     *
     * @param string|int $value
     */
    public function setBlockNumberAttribute($value) {
        $this->attributes['block_number'] = ($value === null ? null : (int)$value);
    }

    /**
     * Get the block date attribute in a localized format.
     *
     * @return CarbonInterface
     */
    public function getBlockDateAttribute() {
        return Carbon::parse($this->attributes['block_date']);
    }

    /**
     * Set the block date attribute by a string date description.
     *
     * @param string $value
     */
    public function setBlockDateAttribute($value) {
        $this->attributes['block_date'] = Carbon::parse($value);
    }

    /**
     * Get the full block number, combined from the day_number and block_number attributes, if available.
     *
     * @return string|null
     */
    public function getFullBlockNumberAttribute() {
        if ($this->day_number == null || $this->block_number == null) {
            return null;
        }
        return $this->day_number . '.' . $this->block_number;
    }

    /**
     * Set the full block number, consisting of the day_number and block_number separated by a period.
     *
     * @param string|null $value
     * @return void
     */
    public function setFullBlockNumberAttribute($value) {
        [$this->day_number, $this->block_number] = ($value === null ? [null, null] : explode('.', $value, 2));
    }

    /**
     * Get the block name preceded with the full block number, if available.
     *
     * @return string|null
     */
    public function getBlocknameAndNumberAttribute() {
        return implode(': ', array_filter([$this->full_block_number, $this->name]));
    }
}
