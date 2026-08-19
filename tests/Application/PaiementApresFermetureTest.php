<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterUneReservation;
use App\Domaine\StatutDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-04 - la fermeture s'apprécie à la validation du formulaire.
 *
 * Le client dispose de ses quinze minutes même si elles franchissent l'heure de
 * fermeture. Sans cette règle, un client validant à 11h59 serait refusé après
 * avoir saisi sa carte, ce que ADR-003 cherche précisément à éviter.
 */
final class PaiementApresFermetureTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-19 09:00';
    }

    /**
     * AC-3 : une réservation validée avant midi peut être payée après.
     */
    public function test_CASE_BOOKING_28_validation_avant_midi_paiement_apres_accepte(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );

        // Cinq minutes avant la fermeture du créneau de 14h : l'immobilisation
        // court jusqu'à 12h10, soit après midi.
        $this->horloge->nousSommesLe('2026-07-20 11:55');
        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
        );

        $this->horloge->nousSommesLe('2026-07-20 12:05');
        $resultat = ($this->service(ConfirmerLePaiement::class))
            ->executer($reservation);

        self::assertTrue(
            $resultat->estConfirme(),
            'midi passé ne refuse pas un formulaire validé avant midi',
        );
        self::assertSame(
            StatutDeReservation::CONFIRMEE,
            ($this->service(ConsulterUneReservation::class))->executer($reservation)->statut(),
        );
        self::assertSame(
            Reference::acompteSortie(Reference::prixDauphins(2)),
            $this->paiement->montantEncaisse($reservation),
        );
    }
}
