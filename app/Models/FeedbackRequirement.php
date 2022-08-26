<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $feedback_id
 * @property int $requirement_id
 * @property int $requirement_status_id
 * @property int $order
 * @property Feedback $feedback
 * @property Requirement $requirement
 * @property RequirementStatus $requirementStatus
 */
class FeedbackRequirement extends Pivot {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedback_requirements';

    protected $fillable_relations = ['feedback', 'requirement', 'requirementStatus'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedback() {
        return $this->belongsTo(Feedback::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requirement() {
        return $this->belongsTo(Requirement::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requirement_status() {
        return $this->belongsTo(RequirementStatus::class);
    }
}
