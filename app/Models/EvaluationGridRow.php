<?php

namespace App\Models;

/**
 * @property int $id
 * @property int evaluation_grid_id
 * @property int evaluation_grid_row_template_id
 * @property string $value
 * @property string $notes
 * @property EvaluationGrid $evaluation_grid
 * @property EvaluationGridRowTemplate $evaluation_grid_row_template
 */
class EvaluationGridRow extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluation_grid_rows';

    /**
     * @var array
     */
    protected $fillable = ['value', 'notes'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluationGrid() {
        return $this->belongsTo(EvaluationGrid::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluationGridRowTemplate() {
        return $this->belongsTo(EvaluationGridRowTemplate::class);
    }
}
