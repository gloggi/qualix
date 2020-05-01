<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $quali_id
 * @property int $requirement_id
 * @property int $order
 * @property boolean|null $passed
 * @property Quali $quali
 * @property Requirement $requirement
 * @property Observation[] $observations
 * @property array $contents
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
    protected $fillable = ['order', 'passed', 'requirement_id'];
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
        return $this->belongsToMany(Observation::class, 'quali_requirement_observations')->withPivot('order')->orderBy('quali_requirement_observations.order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes() {
        return $this->hasMany(QualiRequirementNote::class)->orderBy('order');
    }

    public function getContentsAttribute() {
        return $this->notes->map(function (QualiRequirementNote $note) {
            return [
                'type' => 'text',
                'id' => $note->id,
                'order' => $note->order,
                'content' => $note->notes,
            ];
        })->concat($this->observations->map(function (Observation $observation) {
            return [
                'type' => 'observation',
                'id' => $observation->id,
                'quali_requirement_id' => $observation->pivot->quali_requirement_id,
                'order' => $observation->pivot->order,
                'content' => $observation->content,
                'block' => $observation->block->name,
                'date' => $observation->block->block_date->formatLocalized('%A %d.%m.%Y'),
            ];
        }))->sortBy('order');
    }
}
