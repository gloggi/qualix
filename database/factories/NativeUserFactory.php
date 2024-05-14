<?php

namespace Database\Factories;

use App\Models\NativeUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NativeUserFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NativeUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'name' => ucfirst($this->faker->word),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
