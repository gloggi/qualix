<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $feedback_id
 * @property int $requirement_id
 * @property int $requirement_status_id
 * @property int $order
 * @property string $comment
 * @property Feedback $feedback
 * @property Requirement $requirement
 * @property RequirementStatus $requirement_status
 */
class FeedbackRequirement extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedback_requirements';

    protected $fillable_relations = ['feedback', 'requirement', 'requirement_status'];
    protected $fillable = ['order', 'comment'];

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

    /**
     * Needed for filling the relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requirementStatus() {
        return $this->requirement_status();
    }
}
