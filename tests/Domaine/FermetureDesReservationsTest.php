<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\FermetureDesReservations;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-04 - fermeture des réservations en ligne selon le créneau.
 *
 * Les créneaux du matin ferment à midi la veille, celui de l'après-midi à midi
 * le jour même. La fermeture est effective à partir de 12h00, pas après :
 * 11h59 accepte, 12h00 refuse.
 *
 * L'heure de référence est l'heure locale de l'exploitation.
 */
final class FermetureDesReservationsTest extends TestCase
{
    /**
     * AC-1, AC-2 et AC-4 : les créneaux ferment à midi, la veille pour ceux du
     * matin, le jour même pour celui de l'après-midi.
     */
    public function test_CASE_BOOKING_27_creneaux_ferment_a_midi_la_veille_ou_le_jour_meme(): void
    {
        $fermeture = new FermetureDesReservations();

        $septHeures = Reference::instant('2026-07-20 07:00');
        $dixHeures = Reference::instant('2026-07-20 10:00');
        $quatorzeHeures = Reference::instant('2026-07-20 14:00');

        $veilleAvantMidi = Reference::instant('2026-07-19 11:59');
        self::assertTrue($fermeture->estReservable($septHeures, $veilleAvantMidi));
        self::assertTrue($fermeture->estReservable($dixHeures, $veilleAvantMidi));

        $veilleAMidi = Reference::instant('2026-07-19 12:00');
        self::assertFalse(
            $fermeture->estReservable($septHeures, $veilleAMidi),
            'la fermeture est effective à partir de 12h00, pas après',
        );
        self::assertFalse($fermeture->estReservable($dixHeures, $veilleAMidi));

        self::assertTrue(
            $fermeture->estReservable($quatorzeHeures, Reference::instant('2026-07-20 11:59')),
            'le créneau de 14h ferme le jour même',
        );
        self::assertFalse(
            $fermeture->estReservable($quatorzeHeures, Reference::instant('2026-07-20 12:00')),
        );
    }
}
