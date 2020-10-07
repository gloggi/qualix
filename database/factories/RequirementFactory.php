<?php

use App\Models\Requirement;
use Faker\Generator as Faker;

$factory->define(Requirement::class, function (Faker $faker) {
    $contents = [
        'kann ein SiKo erstellen',
        'macht sich Gedanken über die Machbarkeit einer Aktivität und kann wenn nötig Anpassungen vornehmen',
        'zeigt aktiv, dass er/sie eine Gruppe stufengerecht anleiten kann',
        'kann einen Höck selbständig vorbereiten und leiten',
        'verstösst nicht grob gegen die Kursregeln',
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
        'content' => $faker->unique()->randomElement($contents),
        'mandatory' => $faker->boolean
    ];
});
