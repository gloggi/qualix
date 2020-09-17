<?php

use App\Models\Participant;
use Faker\Generator as Faker;

$factory->define(Participant::class, function (Faker $faker) {
    return [
        'scout_name' => ucfirst($faker->word),
        'group' => str_replace([' GmbH', ' AG'], '', $faker->company)
    ];
});
