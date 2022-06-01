<?php

namespace Database\Factories;

use App\Models\QualiData;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualiDataFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QualiData::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'name' => $this->faker->unique->randomElement([
                'Zwischenquali',
                'Kursrückmeldung',
                'Gefässabschluss',
                'Fördergespräch'
            ]),
        ];
    }
}
