<?php

use App\Models\HitobitoUser;
use App\Models\NativeUser;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => ucfirst($faker->word),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->define(NativeUser::class, function (Faker $faker) {
    return [
        'name' => ucfirst($faker->word),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->define(HitobitoUser::class, function (Faker $faker) {
    return [
        'name' => ucfirst($faker->word),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => null,
        'hitobito_id' => $faker->randomNumber(),
    ];
});
