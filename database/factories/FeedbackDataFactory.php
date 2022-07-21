<?php

namespace Database\Factories;

use App\Models\FeedbackData;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackDataFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FeedbackData::class;

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
