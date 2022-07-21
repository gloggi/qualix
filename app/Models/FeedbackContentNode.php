<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $feedback_id
 * @property int $order
 * @property string $json
 * @property Feedback $feedback
 */
class FeedbackContentNode extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedback_content_nodes';

    /**
     * @var array
     */
    protected $fillable = ['order', 'json'];
    protected $fillable_relations = ['feedback'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedback() {
        return $this->belongsTo(Feedback::class);
    }
}
