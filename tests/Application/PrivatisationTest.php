<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterLesPlacesDisponibles;
use App\Application\CreerReservation;
use App\Application\PrivatiserUnBateau;
use App\Domaine\ResultatDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-05 - privatisation d'un bateau.
 *
 * Une privatisation se facture au forfait du bateau, indépendamment du nombre
 * de participants, et bloque toutes ses places, pas seulement celles occupées.
 * L'autre bateau du créneau, lui, ne bouge pas.
 */
final class PrivatisationTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1, AC-2, AC-3 et AC-5 : la privatisation bloque le bateau entier, au
     * forfait, et laisse l'autre bateau réservable.
     */
    public function test_CASE_BOOKING_29_privatisation_bloque_le_bateau_au_forfait(): void
    {
        $tiKap = $this->sortie(Reference::TI_KAP);
        $grandBleu = $this->sortie(Reference::LE_GRAND_BLEU);

        $privatisation = ($this->service(PrivatiserUnBateau::class))->executer(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::CLIENT_MARIE,
            participants: 4,
        );
        self::assertTrue($privatisation->estAcceptee());

        ($this->service(ConfirmerLePaiement::class))
            ->executer($privatisation->referenceDeReservation());

        self::assertSame(
            Reference::TI_KAP_FORFAIT_PRIVATISATION,
            $this->paiement->montantEncaisse($privatisation->referenceDeReservation()),
            'le montant est le forfait du bateau, indépendant des 4 participants',
        );
        self::assertSame(
            0,
            $this->placesDisponibles($tiKap),
            'les douze places du Ti Kap sont bloquées, pas seulement quatre',
        );

        $placeIndividuelle = ($this->service(CreerReservation::class))
            ->executer($tiKap, Reference::CLIENT_JOHN, adultes: 1);
        self::assertTrue(
            $placeIndividuelle->estRefusee(),
            'aucune place individuelle n\'est proposée sur le Ti Kap à ce créneau',
        );

        self::assertSame(
            Reference::LE_GRAND_BLEU_CAPACITE,
            $this->placesDisponibles($grandBleu),
            'Le Grand Bleu reste réservable au même créneau',
        );
    }

    /**
     * AC-4 : une privatisation est refusée sur un bateau portant déjà des
     * places vendues.
     */
    public function test_CASE_BOOKING_30_privatisation_refusee_si_places_deja_vendues(): void
    {
        $tiKap = $this->sortie(Reference::TI_KAP);
        $this->sortie(Reference::LE_GRAND_BLEU);
        $this->monde->placesVendues($tiKap, 2);

        $refusee = ($this->service(PrivatiserUnBateau::class))->executer(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::CLIENT_JOHN,
            participants: 8,
        );

        self::assertTrue($refusee->estRefusee());
        self::assertSame(
            ResultatDeReservation::MOTIF_BATEAU_DEJA_ENGAGE,
            $refusee->motifDuRefus(),
        );
        self::assertSame(
            Reference::TI_KAP_CAPACITE - 2,
            $this->placesDisponibles($tiKap),
            'les deux réservations existantes ne sont ni annulées ni déplacées',
        );

        $surLautreBateau = ($this->service(PrivatiserUnBateau::class))->executer(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::LE_GRAND_BLEU,
            Reference::CLIENT_JOHN,
            participants: 8,
        );
        self::assertTrue(
            $surLautreBateau->estAcceptee(),
            'Le Grand Bleu, libre, reste privatisable',
        );
    }

    private function sortie(string $bateau): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            $bateau,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function placesDisponibles(string $sortie): int
    {
        return ($this->service(ConsulterLesPlacesDisponibles::class))->pour($sortie);
    }
}
