<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AppliquerUnCode;
use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterLesPlacesDisponibles;
use App\Application\ConsulterUneReservation;
use App\Domaine\ResultatDePaiement;
use App\Domaine\StatutDeReservation;
use App\Domaine\VueDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-07 - paiement en ligne intégral.
 *
 * Le paiement est délégué à un prestataire : nous testons nos réactions à ses
 * réponses, acceptée, refusée, rejouée, jamais son fonctionnement. Aucune
 * donnée de carte n'entre dans l'application, ce que la signature même de
 * l'encaissement garantit : elle ne transporte qu'une référence et un montant.
 */
final class PaiementTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1, AC-3 et AC-5 : un paiement confirmé confirme la réservation,
     * décompte les places, et porte sur la totalité du montant.
     */
    public function test_CASE_BOOKING_09_paiement_confirme_decompte_les_places(): void
    {
        $sortie = $this->sortieBaleines();
        $this->monde->placesVendues($sortie, 6);

        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
            enfants: 1,
        );

        $resultat = $this->confirmerLePaiement($reservation);

        self::assertTrue($resultat->estConfirme());
        self::assertSame(
            StatutDeReservation::CONFIRMEE,
            $this->reservation($reservation)->statut(),
        );
        self::assertSame(
            Reference::prixBaleines(2, 1),
            $this->paiement->montantEncaisse($reservation),
            'la totalité du montant est demandée au prestataire, sans acompte',
        );
        self::assertSame(
            3,
            $this->placesDisponibles($sortie),
            'six vendues puis trois payées sur douze places',
        );
    }

    /**
     * AC-2 : un paiement refusé ne confirme rien et ne décompte aucune place.
     */
    public function test_CASE_BOOKING_10_paiement_refuse_ne_confirme_ni_ne_decompte(): void
    {
        $sortie = $this->sortieBaleines();
        $this->monde->placesVendues($sortie, 6);

        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
            enfants: 1,
        );

        $this->paiement->refuseraLaProchaineTransaction();
        $resultat = $this->confirmerLePaiement($reservation);

        self::assertTrue($resultat->estRefuse());
        self::assertSame(
            ResultatDePaiement::MOTIF_TRANSACTION_REFUSEE,
            $resultat->motifDuRefus(),
        );
        self::assertSame(
            StatutDeReservation::EN_ATTENTE_DE_PAIEMENT,
            $this->reservation($reservation)->statut(),
        );
        self::assertFalse(
            $this->paiement->aEteDebite($reservation),
            'le client n\'est pas débité',
        );

        // Le client peut retenter tant que l'immobilisation court.
        $this->horloge->nousSommesLe('2026-07-18 14:10');
        self::assertTrue($this->confirmerLePaiement($reservation)->estConfirme());

        // Les places n'avaient pas été décomptées par le refus : après le
        // paiement abouti, il en reste bien trois, pas zéro.
        self::assertSame(3, $this->placesDisponibles($sortie));
    }

    /**
     * AC-4 : une double soumission du paiement ne produit qu'un seul débit.
     */
    public function test_CASE_BOOKING_11_double_soumission_un_seul_debit(): void
    {
        $sortie = $this->sortieBaleines();
        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
        );

        // Double clic, ou retour arrière du navigateur.
        $this->confirmerLePaiement($reservation);
        $this->confirmerLePaiement($reservation);

        self::assertSame(
            1,
            $this->paiement->nombreDencaissements(),
            'un seul encaissement est demandé au prestataire',
        );
        self::assertSame(
            Reference::prixBaleines(2),
            $this->paiement->montantEncaisse($reservation),
        );
        self::assertSame(
            StatutDeReservation::CONFIRMEE,
            $this->reservation($reservation)->statut(),
        );
        self::assertSame(
            Reference::TI_KAP_CAPACITE - 2,
            $this->placesDisponibles($sortie),
            'le créneau ne perd que deux places, pas quatre',
        );
    }

    /**
     * AC-6 : un montant dû nul confirme la réservation sans paiement carte.
     */
    public function test_CASE_BOOKING_12_montant_du_nul_confirme_sans_paiement_carte(): void
    {
        $sortie = $this->sortieDauphins();
        $code = $this->monde->bonCadeauAchete(
            Reference::euros(100),
            Reference::JOUR_EN_SAISON,
        );
        $this->horloge->nousSommesLe('2026-07-18 14:00');

        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 2,
        );

        $application = ($this->service(AppliquerUnCode::class))->executer($reservation, $code);
        self::assertSame(
            0,
            $application->montantRestantDu(),
            'un bon de 100 € couvre exactement une réservation de 100 €',
        );

        // L'achat du bon a été encaissé ; la réservation, elle, ne doit rien.
        $encaissementsAvant = $this->paiement->nombreDencaissements();
        $resultat = $this->confirmerLePaiement($reservation);

        self::assertTrue($resultat->estConfirme());
        self::assertSame(
            $encaissementsAvant,
            $this->paiement->nombreDencaissements(),
            'le prestataire de paiement n\'est jamais sollicité pour cette réservation',
        );
    }

    /**
     * AC-7 : un paiement qui aboutit après l'expiration de l'immobilisation,
     * sur une place vendue entre-temps, est refusé et remboursé.
     *
     * Le cas est rare mais il existe : l'immobilisation le réduit, elle ne le
     * supprime pas.
     */
    public function test_CASE_BOOKING_13_paiement_apres_expiration_place_vendue_rembourse(): void
    {
        $sortie = $this->sortieDauphins();
        $this->monde->placesVendues($sortie, Reference::TI_KAP_CAPACITE - 1);

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $clientA = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 1,
        );

        // 14h15 passées : la place est reprise par un autre client.
        $this->horloge->nousSommesLe('2026-07-18 14:16');
        $clientB = $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 1);

        $this->horloge->nousSommesLe('2026-07-18 14:17');
        $resultat = $this->confirmerLePaiement($clientA);

        self::assertTrue($resultat->estRefuse());
        self::assertSame(
            ResultatDePaiement::MOTIF_PLACES_INSUFFISANTES,
            $resultat->motifDuRefus(),
        );
        self::assertSame(
            Reference::prixDauphins(1),
            $this->paiement->montantRembourse($clientA),
            'il est remboursé intégralement sans avoir à le demander',
        );
        self::assertSame(
            StatutDeReservation::CONFIRMEE,
            $this->reservation($clientB)->statut(),
            'la réservation du client B n\'est pas affectée',
        );
        self::assertSame(0, $this->placesDisponibles($sortie));
    }

    private function sortieDauphins(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function sortieBaleines(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_BALEINES,
        );
    }

    private function confirmerLePaiement(string $reservation): ResultatDePaiement
    {
        return ($this->service(ConfirmerLePaiement::class))
            ->executer($reservation);
    }

    private function reservation(string $reference): VueDeReservation
    {
        return ($this->service(ConsulterUneReservation::class))->executer($reference);
    }

    private function placesDisponibles(string $sortie): int
    {
        return ($this->service(ConsulterLesPlacesDisponibles::class))->pour($sortie);
    }
}
