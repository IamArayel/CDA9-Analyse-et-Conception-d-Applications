<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterLeCalendrier;
use App\Application\ConsulterUnCreneau;
use App\Application\ConsulterUneReservation;
use App\Application\CreerReservation;
use App\Application\MettreEnAlerte;
use App\Domaine\ResultatDePaiement;
use App\Domaine\ResultatDeReservation;
use App\Domaine\StatutDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-CANCEL-03 - ce que l'alerte et l'annulation changent côté client.
 *
 * Les deux décisions ont des effets opposés : l'alerte laisse vendre en
 * signalant le risque, l'annulation retire le créneau. Et un client déjà engagé
 * dans le tunnel de paiement est arrêté sans être débité.
 */
final class RepercussionCoteClientTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1, AC-2 et AC-3 : un créneau annulé disparaît de l'offre, un créneau
     * en alerte y reste avec son avertissement.
     */
    public function test_CASE_CANCEL_19_creneau_annule_disparait_creneau_en_alerte_reste(): void
    {
        $this->sortie(Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->sortie(Reference::CRENEAU_APRES_MIDI);

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        self::assertSame(
            [Reference::CRENEAU_MILIEU_DE_MATINEE, Reference::CRENEAU_APRES_MIDI],
            $this->creneauxProposes(),
        );

        ($this->service(MettreEnAlerte::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_APRES_MIDI);

        self::assertContains(
            Reference::CRENEAU_APRES_MIDI,
            $this->creneauxProposes(),
            'le créneau en alerte reste proposé',
        );
        self::assertTrue(
            ($this->service(ConsulterUnCreneau::class))
                ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_APRES_MIDI)
                ->risqueDannulationSignale(),
            'avec le risque signalé',
        );

        ($this->service(AnnulerCreneau::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        self::assertSame(
            [Reference::CRENEAU_APRES_MIDI],
            $this->creneauxProposes(),
            'le créneau annulé n\'est plus proposé à la réservation',
        );
    }

    /**
     * AC-4 et AC-5 : un client en cours de réservation sur un créneau annulé
     * est arrêté, et il n'est pas débité.
     */
    public function test_CASE_CANCEL_20_client_en_cours_arrete_sans_debit(): void
    {
        $sortie = $this->sortie(Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->horloge->nousSommesLe('2026-07-19 11:00');
        $clientA = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
        );

        ($this->service(AnnulerCreneau::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $paiementDeA = ($this->service(ConfirmerLePaiement::class))
            ->executer($clientA);

        self::assertTrue($paiementDeA->estRefuse());
        self::assertSame(
            ResultatDePaiement::MOTIF_CRENEAU_ANNULE,
            $paiementDeA->motifDuRefus(),
            'le motif est l\'annulation du créneau, pas un message générique',
        );
        self::assertTrue(
            $this->paiement->aucunEncaissementDemande(),
            'le paiement est interrompu : aucun débit',
        );
        self::assertSame(
            StatutDeReservation::ANNULEE,
            ($this->service(ConsulterUneReservation::class))->executer($clientA)->statut(),
            'les places immobilisées par A sont libérées',
        );

        $clientB = ($this->service(CreerReservation::class))
            ->executer($sortie, Reference::CLIENT_JOHN, adultes: 1);

        self::assertTrue($clientB->estRefusee());
        self::assertSame(
            ResultatDeReservation::MOTIF_CRENEAU_ANNULE,
            $clientB->motifDuRefus(),
        );
    }

    private function sortie(string $heure): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            $heure,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    /** @return list<string> */
    private function creneauxProposes(): array
    {
        return ($this->service(ConsulterLeCalendrier::class))
            ->executer(Reference::JOUR_EN_SAISON)
            ->creneauxProposes();
    }
}
