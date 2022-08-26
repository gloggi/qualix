<?php

namespace Database\Factories;

use App\Models\RequirementStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequirementStatusFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequirementStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        $names = [
            'erfüllt',
            'unter Beobachtung',
            'nicht erfüllt',
            'bestanden',
            'nicht bestanden',
            'unklar',
            'bestanden, Gespräch ausstehend',
            'nicht bestanden, Gespräch ausstehend',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($names),
            'color' => $this->faker->unique()->randomElement(RequirementStatus::COLORS),
            'icon' => $this->faker->unique()->randomElement(RequirementStatus::ICONS),
        ];
    }
}
