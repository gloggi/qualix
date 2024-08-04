<?php

namespace Database\Factories;

use App\Models\EvaluationGridRowTemplate;
use App\Models\EvaluationGridTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationGridTemplateFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EvaluationGridTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        $names = [
            'Unternehmungsplanung',
            'Unternehmung Durchführung',
            'Wanderplanung',
            'LS Planung',
            'LA Planung',
            'Quartalsprogramm',
            'Lager-SiKo',
            'Aktivitäts-SiKo',
            'Weekendplanung',
            'Durchführung LS',
            'Spontanspiel',
            'Höckleitung',
        ];

        return [
            'name' => $this->faker->randomElement($names),
        ];
    }

    public function withBlocks($count = null) {
        return $this->afterCreating(function (EvaluationGridTemplate $evaluationGridTemplate) use ($count) {
            if (!($course = $evaluationGridTemplate->course)) return;
            if ($course->blocks()->count() == 0) return;

            $blockIds = $evaluationGridTemplate->evaluationGrids()->get()->flatMap->blocks->map->id->unique();
            if ($count === null) $count = $this->faker->biasedNumberBetween(1, 6);
            if ($blockIds->count() == 0) $blockIds = $this->faker->randomElements($course->blocks->map->id, $count);
            $evaluationGridTemplate->blocks()->attach($blockIds);
        });
    }

    public function withRequirements($count = null) {
        return $this->afterCreating(function (EvaluationGridTemplate $evaluationGridTemplate) use ($count) {
            if (!($course = $evaluationGridTemplate->course)) return;
            if ($course->requirements()->count() == 0) return;

            if ($count === null) $count = $this->faker->biasedNumberBetween(1, 3);

            $evaluationGridTemplate->requirements()->attach(
                $this->faker->randomElements($course->requirements->map->id, $count)
            );
        });
    }

    public function withRowTemplates($count = null) {
        if (!$count) $count = $this->faker->biasedNumberBetween(4, 10);
        return $this->has(
            EvaluationGridRowTemplate::factory()->withOrdering()->count($count),
            'evaluationGridRowTemplates'
        );
    }
}
