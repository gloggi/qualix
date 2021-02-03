<?php

namespace Database\Factories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Block::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        $blocks = [
            'Einstieg',
            'Refresh Siko, 3x3, Theorie',
            'UN Planung',
            'Anreise',
            'Kurseinstieg',
            'Rechte + Pflichten als LL',
            'Höck Theorie',
            'Sucht',
            'G&V-Geländegame',
            '1. TN - Höck - Admin',
            'Persönlicher Fortschritt',
            'SpoSpi Muster',
            'Pfadigrundlagen, Beziehungen & Methoden',
            'SpoSpi',
            'Pfadigrundlagen, Beziehungen & Methoden',
            'Gruppenstunde',
            'Rollen im Team (Spiel)',
            'Rollen im Team (Auswertung & Theorie)',
            '2. TN - Höck: J+S & GP',
            'SpoSpi',
            'Motto',
            'Planungszeit GP',
            'Lagersiko Theorie',
            'SpoSpi',
            'Stille Momente auf der Wolfs-/Pfadistufe',
            'Gruppenstunde',
            'Stillen Moment erleben / G+V',
            '3. TN - Höck SiKo',
            'Siko Planung Reserve',
            'SpoSpi',
            'Prä: Postenlauf Hygiene - Ernährung - Apotheke & sensible Daten',
            'Motto',
            'Sicherheitsrel. Aktivitäten',
            'SpoSpi',
            'EHL Rechte & Pflichten',
            'Gruppenstunde',
            'Challenge Baum',
            'Zwischenqualihöck',
            'SpoSpi',
            'SpoSpi',
            'PSA',
            'Unternehmung',
            'Ausbildungsstopp: Aus- und Weiterbildung',
            'Unternehmung',
            'Unternehmung Auswertung + Input Auswertungshöcks',
            'Organisation & Planung Einheit (Theorie)',
            'Organisation & Planung Einheit (Spiel Jahresplan)',
            '4. TN - Höck (z.B. Sicherheitsrelev. Akt)',
            'SpoSpi Reserve',
            'Elternkontakt Theorie',
            'Gruppenstunde',
            'Elternabend Rollenspiel',
            'Pufferzeit für 2. Chancen',
            'LaPla Z: Zeitraffer',
            'Motto',
            'Fremdsportart',
            'Quali-Höcks',
            'Gruppenstunde',
            'Abschlussabend',
            'Quali-Höcks',
            'Kursauswertung',
            'Heim putzen',
            'Quali-Gespräche',
            'Heimreise',
            'Bedürfnisse Wolfs+Pfadistufe',
            'Siko Transfer Stufengerechtigkeit',
            'Spezielli Blüemli',
            'Stufenwahlblock',
            'Gewalt',
            'Heim putzen',
            'Abschluss',
            'Sonstiges',
        ];

        return [
            'name' => $this->faker->randomElement($blocks),
            'day_number' => $this->faker->randomDigit,
            'block_number' => $this->faker->randomDigit,
            'block_date' => $this->faker->date()
        ];
    }
}
