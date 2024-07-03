<?php

namespace App\Models;

/**
 * @property int $id
 * @property int evaluation_grid_template_id
 * @property int $order
 * @property string $criterion
 * @property string $control_type
 * @property string $control_config
 * @property EvaluationGridTemplate $evaluation_grid_template
 * @property EvaluationGridRow[] $evaluation_grid_rows
 */
class EvaluationGridRowTemplate extends Model {

    const CONTROL_TYPES = ['checkbox', 'radiobuttons', 'slider'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluation_grid_row_templates';

    /**
     * @var array
     */
    protected $fillable = ['order', 'criterion', 'control_type'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluationGridTemplate() {
        return $this->belongsTo(EvaluationGridTemplate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function evaluationGridRows() {
        return $this->hasManyThrough(EvaluationGridRow::class, EvaluationGrid::class, 'evaluation_grid_template_id', 'evaluation_grid_id', 'evaluation_grid_template_id', 'id');
    }
}
