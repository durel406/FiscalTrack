<?php

use App\TrackedDocType;
use Illuminate\Database\Seeder;

/**
 * Catalogue des types d'obligations fiscales / sociales — contexte Cameroun (DGI / CNPS).
 * Idempotent : updateOrCreate sur le nom.
 */
class CameroonObligationTypesSeeder extends Seeder
{
    public function run()
    {
        $types = [
            // ---- DGI — impôts courants cabinet ----
            [
                'nom' => 'IGS',
                'periodicite' => 'trimestrielle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'TDL',
                'periodicite' => 'annuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'TVA',
                'periodicite' => 'mensuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'Impôt sur les sociétés (IS)',
                'periodicite' => 'annuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'IRPP / retenues salaires',
                'periodicite' => 'mensuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'Précompte immobilier',
                'periodicite' => 'mensuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'Taxe sur les loyers (Bail)',
                'periodicite' => 'annuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'Patente / contribution des patentes',
                'periodicite' => 'annuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'ACF — Attestation de conformité fiscale',
                'periodicite' => 'annuelle',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'Avis d\'imposition',
                'periodicite' => 'libre',
                'organisme_defaut' => 'DGI',
            ],
            [
                'nom' => 'Quittance de paiement',
                'periodicite' => 'libre',
                'organisme_defaut' => 'DGI',
            ],

            // ---- CNPS ----
            [
                'nom' => 'Cotisations CNPS',
                'periodicite' => 'mensuelle',
                'organisme_defaut' => 'CNPS',
            ],
            [
                'nom' => 'Déclaration nominative CNPS',
                'periodicite' => 'mensuelle',
                'organisme_defaut' => 'CNPS',
            ],
            [
                'nom' => 'ATMP (CNPS)',
                'periodicite' => 'annuelle',
                'organisme_defaut' => 'CNPS',
            ],
            [
                'nom' => 'ACS (CNPS)',
                'periodicite' => 'annuelle',
                'organisme_defaut' => 'CNPS',
            ],
        ];

        foreach ($types as $type) {
            TrackedDocType::updateOrCreate(
                ['nom' => $type['nom']],
                [
                    'periodicite' => $type['periodicite'],
                    'organisme_defaut' => $type['organisme_defaut'],
                ]
            );
        }

        if ($this->command) {
            $this->command->info(count($types).' types d\'obligations Cameroun synchronisés.');
        }
    }
}
