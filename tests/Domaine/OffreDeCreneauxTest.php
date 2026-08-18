<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\OffreDeCreneaux;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-02 - ce qui est proposé un jour donné, règle pure.
 *
 * La saison des baleines court du 15 juin au 31 octobre, bornes incluses. Les
 * trois créneaux, eux, sont proposés tous les jours d'ouverture, quelle que
 * soit la saison : c'est le type de sortie qui varie, pas l'horaire.
 */
final class OffreDeCreneauxTest extends TestCase
{
    /**
     * AC-1, AC-2 et AC-3 : la saison des baleines s'ouvre et se ferme aux
     * bornes incluses.
     */
    public function test_CASE_BOOKING_24_saison_des_baleines_bornes_incluses(): void
    {
        $offre = new OffreDeCreneaux();
        $lesDeux = [Reference::SORTIE_BALEINES, Reference::SORTIE_DAUPHINS];
        $dauphinsSeuls = [Reference::SORTIE_DAUPHINS];

        self::assertSame(
            $dauphinsSeuls,
            $offre->typesDeSortieProposes(Reference::instant('2026-06-14 09:00')),
            'la veille de l\'ouverture, les baleines ne sont pas proposées',
        );
        self::assertSame(
            $lesDeux,
            $offre->typesDeSortieProposes(Reference::instant('2026-06-15 09:00')),
            'le 15 juin est inclus',
        );
        self::assertSame(
            $lesDeux,
            $offre->typesDeSortieProposes(Reference::instant('2026-10-31 09:00')),
            'le 31 octobre est inclus',
        );
        self::assertSame(
            $dauphinsSeuls,
            $offre->typesDeSortieProposes(Reference::instant('2026-11-01 09:00')),
            'le lendemain de la fermeture, les dauphins seuls',
        );

        $troisCreneaux = [
            Reference::CRENEAU_MATIN,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::CRENEAU_APRES_MIDI,
        ];
        foreach (['2026-06-14', '2026-06-15', '2026-10-31', '2026-11-01'] as $jour) {
            self::assertSame(
                $troisCreneaux,
                $offre->creneauxProposes(Reference::instant($jour.' 09:00')),
                'les trois créneaux sont proposés quelle que soit la saison',
            );
        }
    }
}
