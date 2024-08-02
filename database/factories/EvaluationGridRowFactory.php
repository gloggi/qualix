<?php

namespace Database\Factories;

use App\Models\EvaluationGridRow;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationGridRowFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EvaluationGridRow::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        $notes = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Knapp',
            'Bei Verzweigung angehalten',
            'Immer wieder nachgefragt ob die Gruppe Pause machen möchte',
            'Etwas unklar anhand der abgegebenen Dokumente',
            'Mehrfach so ausgeführt',
            'Diverse Male',
            '???',
            'Noch absprechen mit HKL',
            'Könnte besser sein',
            'Nicht immer',
            'Schlüsselstelle nicht erkannt',
            '3 von 7 Methoden eingebaut',
            'Mündliche Begründung war gut',
            'Hat sich an schwierigem Punkt gegen die anderen durchgesetzt',
            'Erklärte die Regeln mit ruhiger Stimme',
            'Gute Stufentransferüberlegungen ausgesprochen',
            'Vorlage aus Cudesch verwendet',
            'Etwas streng, aber machbar',
            'Sehr kreativ',
            'Holte die grosse Verspätung wieder auf',
            'Vorbereitetes Grobprogramm abgeliefert, auf dessen Basis man weiterarbeiten kann',
            'Sehr abwechslungsreich',
        ];

        return [
            'value' => $this->faker->numberBetween(1, 9),
            'notes' => $this->faker->randomElement($notes),
        ];
    }
}
