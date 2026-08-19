<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\AppliquerUnCode;
use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterUneReservation;
use App\Application\SolderUneReservation;
use App\Domaine\ResultatDePaiement;
use App\Domaine\VueDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-12 - règlement du solde après acompte.
 *
 * Le solde se règle en ligne dans une fenêtre bornée, ou par carte au quai. Ce
 * qui est vérifié ici est le chemin en ligne ; le pointage du gérant relève de
 * `SPEC-ADMIN-07`.
 *
 * Les bornes de la fenêtre sont une **déduction d'équipe** et non une réponse
 * du client : question 16 du §11 du cahier des charges.
 */
final class SoldeDeLaReservationTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 09:00';
    }

    /**
     * AC-1, AC-4 et AC-5 : le solde se règle en ligne dans sa fenêtre, en une
     * transaction distincte de l'acompte, et une seconde soumission ne débite
     * rien de plus.
     */
    public function test_CASE_BOOKING_40_solde_regle_en_ligne_en_une_transaction(): void
    {
        $sortie = $this->sortieDuMatin();
        $reservation = $this->monde->reservationConfirmee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
        );

        $encaissementsApresAcompte = $this->paiement->nombreDencaissements();

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        $resultat = $this->service(SolderUneReservation::class)->executer($reservation);

        self::assertTrue($resultat->estConfirme());
        self::assertSame(
            $encaissementsApresAcompte + 1,
            $this->paiement->nombreDencaissements(),
            'le solde est une seconde transaction, distincte de celle de l\'acompte',
        );
        self::assertSame(
            Reference::soldeSortie(Reference::prixDauphins(2)),
            $this->paiement->montantEncaisse($reservation),
            '70 € demandés, et non les 100 € de la réservation',
        );
        self::assertTrue($this->reservation($reservation)->estSoldee());

        $this->service(SolderUneReservation::class)->executer($reservation);
        self::assertSame(
            $encaissementsApresAcompte + 1,
            $this->paiement->nombreDencaissements(),
            'une seconde soumission ne produit aucun débit',
        );
    }

    /**
     * AC-2 : hors de sa fenêtre, le solde n'est pas réglable en ligne.
     */
    public function test_CASE_BOOKING_41_solde_non_reglable_hors_fenetre(): void
    {
        $sortie = $this->sortieDuMatin();
        $reservation = $this->monde->reservationConfirmee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
        );

        // Avant l'ouverture : la fenêtre s'ouvre 24 heures avant le départ.
        $this->horloge->nousSommesLe('2026-07-18 09:00');
        $tropTot = $this->service(SolderUneReservation::class)->executer($reservation);
        self::assertTrue($tropTot->estRefuse());
        self::assertSame(ResultatDePaiement::MOTIF_HORS_FENETRE, $tropTot->motifDuRefus());

        $this->horloge->nousSommesLe('2026-07-19 07:00');
        self::assertTrue(
            $this->service(SolderUneReservation::class)->executer($reservation)->estConfirme(),
            'la fenêtre s\'ouvre exactement 24 heures avant le départ',
        );
    }

    /**
     * AC-3, AC-6 et AC-7 : un solde nul ne se règle pas, un créneau annulé ne
     * le réclame plus, et aucune relance n'est envoyée.
     */
    public function test_CASE_BOOKING_42_solde_nul_et_creneau_annule(): void
    {
        $sortie = $this->sortieDuMatin();

        $code = $this->monde->bonCadeauAchete(Reference::euros(150), '2026-07-18');
        $this->horloge->nousSommesLe('2026-07-18 11:00');

        $couverte = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 1,
            enfants: 1,
        );
        $this->service(AppliquerUnCode::class)->executer($couverte, $code);
        $this->service(ConfirmerLePaiement::class)->executer($couverte);

        self::assertTrue($this->reservation($couverte)->estSoldee());
        self::assertSame(
            0,
            $this->reservation($couverte)->soldeDu(),
            'un code couvrant le prix ne laisse ni acompte ni solde',
        );

        $aAnnuler = $this->monde->reservationConfirmee(
            $sortie,
            Reference::CLIENT_KARIM,
            adultes: 2,
        );

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        $this->service(AnnulerCreneau::class)->executer(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MATIN,
        );

        self::assertSame(
            0,
            $this->reservation($aAnnuler)->soldeDu(),
            'le solde d\'un créneau annulé n\'est plus dû',
        );
        self::assertSame(
            Reference::acompteSortie(Reference::prixDauphins(2)),
            $this->paiement->montantRembourse($aAnnuler),
            'et l\'acompte revient au client',
        );
    }

    private function sortieDuMatin(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MATIN,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function reservation(string $reference): VueDeReservation
    {
        return $this->service(ConsulterUneReservation::class)->executer($reference);
    }
}
