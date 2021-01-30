<?php

namespace Database\Factories;

use App\Models\Observation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObservationFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Observation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        $contents = [
            'beantwortet viele Fallbeispiele im zweiten Ausbildungsstopp',
            'Macht aktiv mit bei den Besprechungen',
            'sagt nichts',
            'hat einiges Vorwissen bezüglich Sicherheitsmodule',
            "Macht wichtige Inputs zum Siko.\nInterpretation: Macht den Eindruck, dass er viel Wissen und Erfahrung im Bereich Wandern/Berg hat (Sicherheitsüberlegungen)",
            'Bei Fallbeispielen, meldet sich freiwillig und antwortet sicher',
            'Ist motiviert und kam mit "Steil und geil"-Gruppe mit. Konnte Inputs zu Sicherheitsrelevanten Aktivitäten geben',
            'sagt im zweiten Teil des Blocks kein Wort',
            'beteiligt sich im zweiten Teil des Blocks wenig',
            'bringt sich viel ein',
            'fragt nach wenn sie nicht drauskommt und beteiligt sich danach mehr',
            'fragt nach wenn er nicht drauskommt, und beteiligt sich danach mehr',
            'erklärt der Gruppe was sie vorher \'gelernt\' hatte',
            'teilt ihre Erfahrungen der Gruppe mit',
            'sagt nichts in grosser Besprechung',
            'ist in Kleingruppen sehr zurückhaltend, sowie auch während dem Essen.',
            'Hat ein gutes Gespür für Thematiken der Wolfsstufe (welche seine eigene Stufe ist)',
            'Gibt passende Inputs',
            "Eher ruhig, aber meldet sich auch und gab passende Inputs.\n-> Interpretation: Erster Tag, er kennt noch niemanden.",
            'Gab passende Inputs',
            'erwähnt in der Gruppendiskussion, dass in Safe Zone TN sich ausruhen können',
            'sagt während 10 Minuten als einziger seiner Gruppe kein Wort',
            'redet am meisten und präsentiert für die Gruppe',
            'redet am meisten und präsentiert für die Gruppe',
            'präsentiert + transferiert für seine Gruppe, hat dabei die Hände in den Hosentaschen + die Beine überkreuzt (es ist zu diesem Zeitpunkt kalt im Raum)',
            'übernimmt mit leiser Stimme von anderer TN, als diese beim Präsentieren etwas nicht lesen kann',
            'bringt sich aktiv in die Planung ein',
            'schreibt alle Ideen der Gruppe auf',
            'Macht sich Gedanken zu Sicherheit',
            'trägt vor',
            'sagt nichts während dem Vortrag',
            'betont die Wichtigkeit der Trinkpausen',
            'Steht als einzige auf um Material abzuklären in Küche',
            'Äussert sich militär-kritisch resp. weist auf militärische Paralellen hin (Bsp. Nei, Kämpfer uusbilde isch nöd guet)',
            'macht gute Transferüberlegungen auf Wolfsstufe',
            'Gähnt häufig',
            'Rege Beteiligung an Ideensammlung',
            'Beteiligt sich aktiv bei Planung',
            'beteiligt sich aktiv an der Planung und schreibt die Ideen auf, übernimmt die Leitung der Gruppe',
            'dachte bei der Planung des Geländegames an das siko. Konnte sich gut in der Gruppe einfügen.',
            'sagt während dem Teamspiel und der Auswertung nur zwei Mal etwas: zuerst beim Raten wie die Figur aussehen muss, und dann noch als es darum geht, eine einfachere, weniger genaue Lösung zu wählen',
            'Ist während dem Block am Handy',
            'erklärt der restlichen Gruppe die ganze Situation',
            'pausiert und zeichnet die einzelnen Teile',
            'macht die geometrischen Zeichnungen',
            'Setzt sich als Teamleiter  (in Aufgabe eingeweiht) nicht ganz durch',
            'Beteiligt sich nur wenig, da er eigene Kompetenzen nicht im geometrischen sieht',
            'Widerspricht der Gruppenleitung einige Male und besteht auf seiner Meinung',
            "Hat sehr schnell angefangen zu zeichnen.\n-> Ist motiviert und arbeitet schnell, jedoch könnte er zuerst mehr planen oder Auftrag/Anweisung hinterfragen (Figur war falsch beschrieben)",
            'Beobachtet Gruppe und bringt sich später ein und hilft mit.',
            'Eher ruhig, brachte sich gut ein und half mit',
            'Konnte sich geometrische Figur nicht genau vorstellen, hat aber der Gruppe den Auftrag erklärt. Schlussendlich war die Figur falsch, aber die Gruppe hat perform/eine Figur erstellen können und als Gruppe funktioniert',
            'sah die Pyramide und erklärte das Vorgehen gut. Er machte jedoch sehr viel selbst und übergibt den Anderen wenige Aufgaben.',
            'Macht sich gute Überlegungen (Klebemaschen für die einzelnen Teile) und unterstützt Spatz, um möglichst schnell eine gute Pyramide bauen zu können.',
            'Unterstützt Teamleiter mit Überlegungen (wie zeichnet man die Vorlage am besten, um mit 1 Stk. zu basteln) und macht aktiv mit.',
            'Verhält sich zu Beginn sehr ruhig und beobachtet nur. Nach einiger Zeit schnappt sie sich aber Papier und Stift und hilft auch mit.',
            'Tritt selbstbewusst auf und erklärt Spiel, übernimmt häufig die Leitung (Vorstellen und erklären) mit seiner lauten und deutlichen Stimme',
            'Wirkt eher unscheinbar',
            'Macht sich Überlegung bezüglich der Uhrzeit der Aktivität und kann daraus Sicherheitsüberlegungen ableiten.',
            'geht um Tisch herum um auf den Bildschirm des Laptops zu sehen (map.wanderland.ch, MZB)',
            'sagt, dass in seiner Abteilung fast nie MZB gemacht werden',
            'holt für den Expertenaustausch als einziger Kompass-Experte einen Kompass',
            'sagt wenig aber macht sich viele notizen',
            'erklärt den anderen die MZB',
            'beteiligt sich aktiv beim MZB Theorie-Teil',
            'Macht ausführliche SiKo Erklärungen',
            'eher knapp und wenig enthusiastisch beim Erklären der MZB in Kleingruppe',
            'Hat die Orientierung im Griff',
            'Hat seinen Teil gut zusammengefasst und das wichtigste hervorgehoben.',
            'Hatten eine gute Gruppenatmosphäre. Haben zusammen die Route erarbeitet.',
            'beteiligt sich an Punktewahl für MZB',
            'füllt am PC die MZB aus',
            'meldet sich freiwillig, das SiKo / 3x3 der Gruppe zu machen',
            'koordiniert die Verteilung der Aufgaben und erstellt einen Gruppenchat',
            'füllt die erste Zeile des 3x3 aus und nimmt sich dazu die Broschüre Sicherheit zur Hilfe',
            'übernimmt den Teil am Computer',
            'Will zu Fuss gehen nicht mit dem Velo und kommunizert das klar',
            'Sitzen alle um die Karte, statt sich die Aufgaben aufzuteilen',
            'Übernimmt zu Beginn Planungsaufgabe am Computer (Route)',
            'oft beteiligt',
            'rege Beteiligung',
            'Beteiligt sich wenn er soll, lag aber mit dem Kopf auf seinen Armen.',
            'Hat ein Flugzeug gefaltet',
            'Hat Siko verbessert aber immernoch PONR und Ausstiegspunkte vergessen.',
            'beteiligt sich nicht aktiv an der Planung der Unternehmung. Sie hat die Aufgabe die Materialliste zu machen und sucht Ausreden, um ihre Aufgabe  nicht jetzt machen zu müssen, sondern erst zu Hause. Auch mit Aufforderung anderer TN aus der Gruppe.',
            'Übernimmt den Lead bei der Planung und animiert die Anderen ihre zugeteilten Aufgaben schon zu erledigen als nur einfach da zu sitzen und zu warten bis der Block zu Ende ist.',
            'nutzt die Zeit nicht, um schon mit dem Siko anzufangen. Er sagt, dass er zu Hause ein altes Siko kopieren und abändern möchte.',
            'verteilt die zu machenden Aufgaben und sucht schon einmal die Busverbindungen heraus.',
        ];

        return [
            'content' => $this->faker->randomElement($contents),
            'impression' => $this->faker->randomElement([0, 1, 2]),
        ];
    }
}
