<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $quali_requirement_id
 * @property int $order
 * @property string $notes
 * @property QualiRequirement $quali_requirement
 */
class QualiRequirementNote extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quali_requirement_notes';

    /**
     * @var array
     */
    protected $fillable = ['order', 'notes'];
    protected $fillable_relations = ['quali_requirement'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quali_requirement() {
        return $this->belongsTo(QualiRequirement::class);
    }
}
