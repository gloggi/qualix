<?php

use App\Models\Block;
use App\Models\Course;
use App\Models\Observation;
use App\Models\Participant;
use App\Models\Requirement;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement([
            'Basiskurs',
            'Aufbaukurs',
            'Pano',
            'Topkurs'
        ]) . ' ' . $faker->biasedNumberBetween(2018, 2030),
    ];
});

$factory->afterCreating(Course::class, function (Course $course, Faker $faker) {
    $course->participants()->saveMany(factory(Participant::class, 10)->make());
    $course->requirements()->saveMany(factory(Requirement::class, 4)->make());
    $course->blocks()->saveMany(factory(Block::class, 10)->make());
    $course->users()->saveMany(factory(User::class, 3)->make());
    $blocks = $course->blocks()->pluck('id')->all();
    $requirements = $course->requirements()->pluck('id')->all();
    $participants = $course->participants()->pluck('id')->all();
    $course->users->each(function(User $user) use($course, $blocks, $faker) {
        $course->participants->each(function(Participant $participant) use($user, $blocks, $faker) {
            $participant->observations()->saveMany(factory(Observation::class, $faker->biasedNumberBetween(0, 5))
                ->make([
                    'block_id' => $faker->randomElement($blocks),
                    'user_id' => $user->id,
                ])
            );
        });
    });
    /** @var \Illuminate\Support\Collection $observations */
    $observations = $course->observations;
    $multiParticipantObservations = collect($faker->randomElements($observations->all(), floor($observations->count() * 0.2)));
    $multiParticipantObservations->each(function(Observation $observation) use($participants, $faker) {
        $observation->participants()->sync($faker->randomElements($participants, $faker->biasedNumberBetween(2, 4)));
    });
    $observations->each(function(Observation $observation) use($requirements, $faker) {
        $observation->requirements()->attach($faker->randomElements($requirements, $faker->biasedNumberBetween(0, 3)));
    });
});
