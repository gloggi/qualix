<?php

namespace Database\Factories;

use App\Models\EvaluationGridRowTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationGridRowTemplateFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EvaluationGridRowTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        $criteria = [
            'Genügend Pausen eingeplant',
            'Sinnvolle Route gewählt',
            'Stufengerechtigkeit',
            'Gruppe im Griff haben',
            'Als Leitung präsent sein',
            'Aktive Leitung',
            'Karte lesen',
            'Holt sich Hilfe falls nötig',
            'Achtet auf die Gruppe',
            'J+S-Vorgaben eingehalten',
            'Mindestens 30 Minuten Dauer',
            'Inhalt aus Themenbereichen',
            'Sportliche Betätigung',
            'Spielerische Vermittlung der Lerninhalte',
            'Alle TN sind im Block aktiv beteiligt',
            'Pfadibeziehungen abgedeckt',
            'Pfadimethoden eingebaut',
            'Relevante Gefahren identifizieren',
            'Massnahmen definiert',
            'Massnahmen umgesetzt',
            'Sicherheitsüberlegungen ausgesprochen',
            'Grundstruktur verwendet',
            'Grobprogramm',
            'Budget',
            'Passendes Motto',
            'Kreativität',
            'Zeitmanagement im Griff',
            'Kommt vorbereitet an den Höck',
            'Verwendete Methoden',
            'Gruppe kommt vorwärts',
        ];

        return [
            'criterion' => $this->faker->randomElement($criteria),
            'control_type' => $this->faker->randomElement(EvaluationGridRowTemplate::CONTROL_TYPES),
        ];
    }

    public function withOrdering() {
        return $this->sequence(fn ($sequence) => ['order' => $sequence->index + 1]);
    }
}
