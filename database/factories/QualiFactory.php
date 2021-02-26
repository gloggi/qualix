<?php

namespace Database\Factories;

use App\Models\ParticipantObservation;
use App\Models\Quali;
use App\Models\QualiContentNode;
use App\Models\QualiData;
use App\Models\Requirement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class QualiFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quali::class;

    private $participantIdSequence = null;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [];
    }

    public function forParticipants() {
        return $this->state(function (array $attributes, QualiData $qualiData) {
            return ['participant_id' => $this->getParticipantIdSequence($qualiData)];
        });
    }

    private function getParticipantIdSequence(QualiData $qualiData) {
        if (!$this->participantIdSequence) {
            $this->participantIdSequence = new Sequence(...$qualiData->course->participants->map->id);
        }
        return $this->participantIdSequence;
    }

    public function withContents() {
        return $this->has(
            QualiContentNode::factory()->count($this->faker->biasedNumberBetween(4, 10)),
            'contentNodes'
        );
    }

    public function withRequirements() {
        return $this->afterCreating(function (Quali $quali) {
            if (!($qualiData = $quali->quali_data)) return;
            if (!($course = $qualiData->course)) return;

            $quali->requirements()->sync($course->requirements->mapWithKeys(function (Requirement $requirement) {
                return [$requirement->id => [
                    'order' => $this->faker->biasedNumberBetween(0, 10),
                    'passed' => $this->faker->randomElement([null, 0, 1]),
                ]];
            }));
        });
    }

    public function withObservations() {
        return $this->afterCreating(function (Quali $quali) {
            if (!($participant = $quali->participant)) return;
            if (($observationCount = $participant->participant_observations()->count()) == 0) return;
            $participantObservations = $this->faker->randomElements(
                $participant->participant_observations->all(),
                $this->faker->biasedNumberBetween(min($observationCount, 2), min($observationCount, 5))
            );

            $quali->participant_observations()->sync(collect($participantObservations)
                ->mapWithKeys(function (ParticipantObservation $participantObservation) {
                    return [$participantObservation->id => ['order' => $this->faker->biasedNumberBetween(0, 10)]];
                })
            );
        });
    }
}
