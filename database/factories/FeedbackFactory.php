<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\FeedbackContentNode;
use App\Models\FeedbackData;
use App\Models\ParticipantObservation;
use App\Models\Requirement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class FeedbackFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Feedback::class;

    private $participantIdSequence = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [];
    }

    public function forParticipants() {
        return $this->state(function (array $attributes, FeedbackData $feedbackData) {
            return ['participant_id' => $this->getParticipantIdSequence($feedbackData)];
        });
    }

    private function getParticipantIdSequence(FeedbackData $feedbackData) {
        if (!$this->participantIdSequence) {
            $this->participantIdSequence = new Sequence(...$feedbackData->course->participants->map->id);
        }
        return $this->participantIdSequence;
    }

    public function withContents() {
        return $this->has(
            FeedbackContentNode::factory()->count($this->faker->biasedNumberBetween(4, 10)),
            'contentNodes'
        );
    }

    public function withRequirements() {
        return $this->afterCreating(function (Feedback $feedback) {
            if (!($feedbackData = $feedback->feedback_data)) return;
            if (!($course = $feedbackData->course)) return;

            $feedback->requirements()->sync($course->requirements->mapWithKeys(function (Requirement $requirement) {
                return [$requirement->id => [
                    'order' => $this->faker->biasedNumberBetween(0, 10),
                    'passed' => $this->faker->randomElement([null, 0, 1]),
                ]];
            }));
        });
    }

    public function withObservations() {
        return $this->afterCreating(function (Feedback $feedback) {
            if (!($participant = $feedback->participant)) return;
            if (($observationCount = $participant->participant_observations()->count()) == 0) return;
            $participantObservations = $this->faker->randomElements(
                $participant->participant_observations->all(),
                $this->faker->biasedNumberBetween(min($observationCount, 2), min($observationCount, 5))
            );

            $feedback->participant_observations()->sync(collect($participantObservations)
                ->mapWithKeys(function (ParticipantObservation $participantObservation) {
                    return [$participantObservation->id => ['order' => $this->faker->biasedNumberBetween(0, 10)]];
                })
            );
        });
    }
}
