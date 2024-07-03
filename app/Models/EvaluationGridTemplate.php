<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $name
 * @property int $course_id
 * @property Course $course
 * @property EvaluationGrid[] $evaluation_grids
 * @property EvaluationGridRowTemplate[] $evaluation_grid_row_templates
 * @property Requirement[] $requirements
 * @property Block[] $blocks
 */
class EvaluationGridTemplate extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluation_grid_templates';

    /**
     * @var array
     */
    protected $fillable = ['name', 'course_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course() {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluationGrids() {
        return $this->hasMany(EvaluationGrid::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluationGridRowTemplates() {
        return $this->hasMany(EvaluationGridRowTemplate::class)
            ->orderBy('evaluation_grid_row_templates.order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requirements() {
        return $this->belongsToMany(Requirement::class, 'evaluation_grid_templates_blocks');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blocks() {
        return $this->belongsToMany(Block::class, 'evaluation_grid_templates_blocks')
            ->orderBy('blocks.block_date')
            ->orderBy('blocks.day_number')
            ->orderBy('blocks.block_number')
            ->orderBy('blocks.name')
            ->orderBy('blocks.id');
    }
}
