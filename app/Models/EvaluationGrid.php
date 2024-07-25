<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $evaluation_grid_template_id
 * @property int $block_id
 * @property int $user_id
 * @property EvaluationGridTemplate $evaluation_grid_template
 * @property Participant[] $participants
 * @property Block $block
 * @property User $user
 * @property EvaluationGridRow[] $rows
 */
class EvaluationGrid extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluation_grids';

    /**
     * @var array
     */
    protected $fillable = ['evaluation_grid_template_id', 'block_id', 'user_id'];
    protected $fillable_relations = ['block'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluationGridTemplate() {
        return $this->belongsTo(EvaluationGridTemplate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants() {
        return $this->belongsToMany(Participant::class, 'evaluation_grids_participants');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block() {
        return $this->belongsTo(Block::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rows() {
        return $this->hasMany(EvaluationGridRow::class)
            ->select('evaluation_grid_rows.*') // make sure not to select the rows' fields during the join
            ->join('evaluation_grid_row_templates', 'evaluation_grid_rows.evaluation_grid_row_template_id', '=', 'evaluation_grid_row_templates.id')
            ->orderBy('evaluation_grid_row_templates.order');
    }

    /**
     * Convenience method for getting the evaluation grid's name from the related evaluation grid template.
     * @return string
     */
    public function getNameAttribute() {
        return $this->evaluation_grid_template->name;
    }

    /**
     * Convenience method for setting the evaluation grid's name in the related evaluation grid template.
     * @param $name
     * @return void
     */
    public function setNameAttribute($name) {
        $this->evaluation_grid_template->attributes['name'] = $name;
    }

    /**
     * Display name of the evaluation grid including the participant's name and block name.
     * @return string
     */
    public function getDisplayNameAttribute() {
        return trans('t.models.evaluation_grid.display_name', [
            'evaluation_grid_name' => $this->getNameAttribute(),
            'participant_name' => $this->participant->scout_name,
            'block_name' => $this->block->name
        ]);
    }
}
