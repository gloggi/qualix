<?php

namespace Database\Factories;

use App\Models\EvaluationGrid;
use App\Models\EvaluationGridRow;
use App\Models\EvaluationGridTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationGridFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EvaluationGrid::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [];
    }

    public function withBlock($blockId = null) {
        return $this->state(function (array $state, ?EvaluationGridTemplate $evaluationGridTemplate = null) use ($blockId) {
            if ($evaluationGridTemplate == null) {
                $evaluationGridTemplate = EvaluationGridTemplate::find($state['evaluation_grid_template_id']);
            }
            if ($blockId == null) {
                $options = $evaluationGridTemplate->blocks()->count() > 0 ?
                    $evaluationGridTemplate->blocks()->get() :
                    $evaluationGridTemplate->course->blocks()->get();
                $blockId = $this->faker->randomElement($options->map->id);
            }
            return [ 'block_id' => $blockId ];
        });
    }

    public function fromRandomUser() {
        return $this->state(function (array $state, ?EvaluationGridTemplate $evaluationGridTemplate = null) {
            if ($evaluationGridTemplate == null) {
                $evaluationGridTemplate = EvaluationGridTemplate::find($state['evaluation_grid_template_id']);
            }
            return [
                'user_id' => $this->faker->randomElement($evaluationGridTemplate->course->users->map->id),
            ];
        });
    }

    public function maybeMultiParticipant($percentage = 20) {
        return $this->afterCreating(function (EvaluationGrid $evaluationGrid) use ($percentage) {
            if (!($evaluationGridTemplate = $evaluationGrid->evaluationGridTemplate()->first())) return;
            if (!($course = $evaluationGridTemplate->course)) return;
            $numParticipants = $course->participants()->count();
            if ($numParticipants == 0) return;

            $numParticipants = 1;
            // Only a fraction of evaluation grids are multi-participant
            if ($this->faker->randomNumber(2) < $percentage) $numParticipants = min($numParticipants, $this->faker->biasedNumberBetween(2, min($numParticipants, 4)));

            $evaluationGrid->participants()->sync(
                $this->faker->randomElements($course->participants->map->id->all(), $numParticipants)
            );
        });
    }

    public function withRows() {
        return $this->afterCreating(function (EvaluationGrid $evaluationGrid) {
            if (!($evaluationGridTemplate = $evaluationGrid->evaluationGridTemplate()->first())) return;
            if (!($rowTemplates = $evaluationGridTemplate->evaluationGridRowTemplates()->get())) return;
            if ($rowTemplates->count() == 0) return;

            EvaluationGridRow::factory()
                ->for($evaluationGrid)
                ->forEachSequence(...$rowTemplates->map(fn ($rowTemplate) => ['evaluation_grid_row_template_id' => $rowTemplate->id])->all())
                ->create();
        });
    }
}
