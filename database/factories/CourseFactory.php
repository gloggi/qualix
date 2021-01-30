<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Course;
use App\Models\Observation;
use App\Models\Participant;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'name' => $this->faker->randomElement([
                    'Basiskurs',
                    'Aufbaukurs',
                    'Pano',
                    'Topkurs'
                ]) . ' ' . $this->faker->biasedNumberBetween(2018, 2030),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Course $course) {
            $course->participants()->saveMany(Participant::factory()->count(10)->make());
            $course->requirements()->saveMany(Requirement::factory()->count(4)->make());
            $course->blocks()->saveMany(Block::factory()->count(10)->make());
            $course->users()->saveMany(User::factory()->count(3)->make());
            $blocks = $course->blocks()->pluck('id')->all();
            $requirements = $course->requirements()->pluck('id')->all();
            $participants = $course->participants()->pluck('id')->all();
            $course->users->each(function (User $user) use ($course, $blocks) {
                $course->participants->each(function (Participant $participant) use ($user, $blocks) {
                    $participant->observations()->saveMany(Observation::factory()
                        ->count($this->faker->biasedNumberBetween(0, 5))
                        ->make([
                            'block_id' => $this->faker->randomElement($blocks),
                            'user_id' => $user->id,
                        ])
                    );
                });
            });
            /** @var \Illuminate\Support\Collection $observations */
            $observations = $course->observations;
            $multiParticipantObservations = collect($this->faker->randomElements($observations->all(), floor($observations->count() * 0.2)));
            $multiParticipantObservations->each(function (Observation $observation) use ($participants) {
                $observation->participants()->sync($this->faker->randomElements($participants, $this->faker->biasedNumberBetween(2, 4)));
            });
            $observations->each(function (Observation $observation) use ($requirements) {
                $observation->requirements()->attach($this->faker->randomElements($requirements, $this->faker->biasedNumberBetween(0, 3)));
            });
        });
    }
}
