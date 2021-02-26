<?php

namespace Database\Factories;

use App\Models\Course;
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
}
