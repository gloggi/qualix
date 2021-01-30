<?php

namespace Database\Factories;

use App\Models\HitobitoUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class HitobitoUserFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HitobitoUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'name' => ucfirst($this->faker->word),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => null,
            'hitobito_id' => $this->faker->randomNumber(),
        ];
    }
}
