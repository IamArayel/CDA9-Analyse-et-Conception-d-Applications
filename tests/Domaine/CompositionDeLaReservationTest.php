<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\CompositionDeLaReservation;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-01 - qui peut composer une réservation, règle pure.
 *
 * Aucun minimum de personnes n'est imposé : c'est la règle corrigée en v3, la
 * v1 et la v2 exigeaient à tort deux personnes. Mais une réservation sans
 * aucun participant n'a pas de sens, et un enfant seul non plus.
 */
final class CompositionDeLaReservationTest extends TestCase
{
    /**
     * AC-2 et AC-3 : une réservation sans participant, ou sans adulte alors
     * qu'un enfant est déclaré, est refusée.
     *
     * Le second refus repose sur une hypothèse d'équipe : le client n'a jamais
     * évoqué de mineur non accompagné, mais toutes les règles écrites
     * l'autorisaient.
     */
    public function test_CASE_BOOKING_21_reservation_sans_participant_ou_sans_adulte_refusee(): void
    {
        $composition = new CompositionDeLaReservation();

        self::assertFalse(
            $composition->estValide(adultes: 0, enfants: 0),
            'une réservation sans personne n\'existe pas',
        );
        self::assertFalse(
            $composition->estValide(adultes: 0, enfants: 2),
            'un adulte au moins est requis dès qu\'un enfant est déclaré',
        );
        self::assertSame(
            CompositionDeLaReservation::MOTIF_ADULTE_REQUIS,
            $composition->motifDuRefus(adultes: 0, enfants: 2),
        );

        self::assertTrue(
            $composition->estValide(adultes: 1, enfants: 0),
            'un client seul est accepté : aucun minimum de personnes',
        );
    }
}
