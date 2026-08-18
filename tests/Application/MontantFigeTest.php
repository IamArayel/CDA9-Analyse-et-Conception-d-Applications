<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterUneReservation;
use App\Application\ModifierUnTarif;
use App\Domaine\VueDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-06 et SPEC-ADMIN-02 - le montant est figé à la validation.
 *
 * Un client ne peut pas être débité d'un montant différent de celui qui lui a
 * été présenté. Le montant est donc recopié sur la réservation, il ne suit pas
 * la grille des tarifs.
 */
final class MontantFigeTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * SPEC-BOOKING-06 AC-3 et AC-4, SPEC-ADMIN-02 AC-4 : le montant affiché est
     * celui encaissé, malgré un changement de tarif entre-temps.
     */
    public function test_CASE_BOOKING_32_montant_affiche_egal_au_montant_encaisse(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );

        // Le client a choisi l'anglais : le tarif reste en euros pour autant.
        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 2,
        );
        $recapitulatif = $this->reservation($reservation);

        self::assertSame(
            Reference::prixDauphins(2),
            $recapitulatif->montantDu(),
            'le récapitulatif affiche 100 € pour deux adultes',
        );
        self::assertSame(
            'EUR',
            $recapitulatif->devise(),
            'aucune conversion de devise, quelle que soit la langue choisie',
        );

        (new ModifierUnTarif())->executer(
            Reference::SORTIE_DAUPHINS,
            prixAdulte: Reference::euros(55),
            prixEnfant: Reference::DAUPHINS_PRIX_ENFANT,
        );

        (new ConfirmerLePaiement($this->horloge, $this->paiement, $this->messages))->executer($reservation);

        self::assertSame(
            Reference::prixDauphins(2),
            $this->paiement->montantEncaisse($reservation),
            'le montant est figé à la validation du formulaire, il ne suit pas la grille',
        );
    }

    private function reservation(string $reference): VueDeReservation
    {
        return (new ConsulterUneReservation($this->horloge))->executer($reference);
    }
}
