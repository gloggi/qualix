<?php

use App\Models\Block;
use App\Models\Course;
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
        ]) . ' ' . $faker->year,
    ];
});

$factory->afterCreating(Course::class, function (Course $course, Faker $faker) {
    $course->participants()->saveMany(factory(Participant::class, 20)->make());
    $course->requirements()->saveMany(factory(Requirement::class, 5)->make());
    $course->blocks()->saveMany(factory(Block::class, 20)->make());
    $course->users()->saveMany(factory(User::class, 5)->make());
});
