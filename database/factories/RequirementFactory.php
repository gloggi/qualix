<?php

namespace Database\Factories;

use App\Models\Requirement;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequirementFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Requirement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        $contents = [
            'kann ein SiKo erstellen',
            'macht sich Gedanken über die Machbarkeit einer Aktivität und kann wenn nötig Anpassungen vornehmen',
            'zeigt aktiv, dass er/sie eine Gruppe stufengerecht anleiten kann',
            'kann einen Höck selbständig vorbereiten und leiten',
            'kann ein stufengerechtes Pfadiprogramm gestalten',
            'kann sich Überlegungen zu stufengerechtem und ausgewogenem Programm machen',
            'kann ein Team und eine Gruppe aktiv anleiten',
            'kennt alle Schritte der Lagerplanung',
            'kann in Planung und Durchführung Sicherheitsüberlegungen anstellen',
            'kann einen Lageraktivitätsblock planen',
            'kann eine Unternehmung planen',
            'kann ein Quartalsprogramm planen',
            'kann die sieben Pfadimethoden anwenden',
            'kann einen LS durchführen und leiten',
            'bringt sich bei der Planung aktiv in die Gruppe ein',
            'zeigt Eigeninitiative und übernimmt Verantwortung',
            'nimmt aktiv an den Blöcken teil',
            'ist bereit, sich mit der eigenen Rolle als Pfadi und dem Pfadigedanken auseinander zu setzen',
            'kennt die Bedürfnisse der Wölfli',
            'kann situationsgerecht vor einer Gruppe auftreten',
            'kann eine Wanderung planen, durchführen und auswerten',
            'erkennt sicherheitsrelevante Aktivitäten und kann ein Si-Ko dafür erstellen',
            'verhält sich entsprechend der zukünftigen Rolle als Lagerleitung und Stufenleitung',
            'muss ein geiler Siech sein',
        ];

        return [
            'content' => $this->faker->unique()->randomElement($contents),
            'mandatory' => $this->faker->boolean
        ];
    }
}
