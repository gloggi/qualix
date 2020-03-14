<?php

namespace App\Models;

/**
 * @property int $quali_id
 * @property int $requirement_id
 * @property boolean|null $passed
 * @property string $notes
 * @property Quali $quali
 * @property Requirement $requirement
 * @property Observation[] $observations
 */
class QualiRequirement extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quali_requirements';

    /**
     * @var array
     */
    protected $fillable = ['passed', 'notes'];
    protected $fillable_relations = ['quali', 'requirement'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quali() {
        return $this->belongsTo(Quali::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requirement() {
        return $this->belongsTo(Requirement::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function observations() {
        return $this->belongsToMany(Observation::class, 'observations_quali_requirements', 'quali_requirement_id');
    }
}
