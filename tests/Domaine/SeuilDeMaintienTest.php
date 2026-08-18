<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\SeuilDeMaintien;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-03 - le seuil de maintien d'une sortie, règle pure.
 *
 * « Une sortie est maintenue à partir de 6 inscrits » (REQ-002). Le mot
 * « à partir de » est le cœur de la règle : 6 suffit. C'est une règle de
 * décision, sans base ni réseau, donc de niveau domaine.
 *
 * L'instant est un paramètre, jamais lu sur l'horloge système (ADR-005,
 * option A pour les calculs purs).
 */
final class SeuilDeMaintienTest extends TestCase
{
    /**
     * AC-5 : un créneau comptant exactement 6 inscrits au contrôle est
     * maintenu.
     *
     * Le cas à 5 inscrits, avec son annulation et ses remboursements, est
     * vérifié au niveau application par la tâche du contrôle des 24 heures.
     */
    public function test_CASE_BOOKING_06_seuil_exactement_atteint_maintient_la_sortie(): void
    {
        $heureDeDepart = Reference::instant('2026-07-20 10:00');
        $vingtQuatreHeuresAvant = Reference::instant('2026-07-19 10:00');

        $decision = (new SeuilDeMaintien())->decider(
            nombreDInscrits: 6,
            heureDeDepart: $heureDeDepart,
            maintenant: $vingtQuatreHeuresAvant,
        );

        self::assertTrue(
            $decision->sortieEstMaintenue(),
            'six inscrits atteignent le seuil : la sortie reste programmée',
        );
        self::assertFalse(
            $decision->remboursementEstDu(),
            'une sortie maintenue ne rembourse personne',
        );
    }
}
