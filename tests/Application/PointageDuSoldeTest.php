<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\ConsulterLesPaiements;
use App\Application\ConsulterUneReservation;
use App\Application\ExporterLePlanning;
use App\Application\PointerLeSolde;
use App\Domaine\VueDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-ADMIN-07 - pointage d'un solde encaissé au quai.
 *
 * **L'outil n'effectue aucune transaction.** Le gérant encaisse sur son
 * terminal bancaire habituel, puis enregistre le fait. Ce que ces tests
 * vérifient avant tout, c'est qu'aucun appel ne part vers le prestataire.
 *
 * Le pointage est réversible, et un pointage annulé puis refait laisse **trois
 * écritures**, pas un drapeau écrasé deux fois : c'est ce qui distingue un
 * journal d'une colonne.
 */
final class PointageDuSoldeTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 09:00';
    }

    /**
     * AC-1, AC-4 et AC-6 : le gérant pointe sans qu'aucune transaction ne
     * parte, le planning distingue les deux états, et pointer une réservation
     * déjà soldée est sans effet.
     */
    public function test_CASE_ADMIN_18_pointage_du_solde_sans_transaction(): void
    {
        $sortie = $this->sortieDuMatin();
        $enLigne = $this->monde->reservationSoldee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $auQuai = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_JOHN, adultes: 2);

        $this->horloge->nousSommesLe('2026-07-20 06:30');
        self::assertSame(
            [true, false],
            $this->soldesDuPlanning(),
            'le planning distingue qui a soldé de qui reste à encaisser',
        );

        $encaissementsAvant = $this->paiement->nombreDencaissements();
        $this->service(PointerLeSolde::class)->executer($auQuai);

        self::assertTrue($this->reservation($auQuai)->estSoldee());
        self::assertSame(
            $encaissementsAvant,
            $this->paiement->nombreDencaissements(),
            'aucune transaction n\'est demandée au prestataire : l\'outil enregistre un fait',
        );
        self::assertSame([true, true], $this->soldesDuPlanning());

        // Le gérant ne peut pas savoir de tête qui a réglé en ligne.
        $this->service(PointerLeSolde::class)->executer($enLigne);
        self::assertTrue($this->reservation($enLigne)->estSoldee());
        self::assertSame(
            $encaissementsAvant,
            $this->paiement->nombreDencaissements(),
        );
    }

    /**
     * AC-2, AC-3 et AC-5 : le pointage est réversible, chaque geste est
     * conservé, et une réservation annulée n'est pas pointable.
     */
    public function test_CASE_ADMIN_19_pointage_reversible_et_trace(): void
    {
        $sortie = $this->sortieDuMatin();
        $reservation = $this->monde->reservationConfirmee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 2,
        );
        // Prise ici, et non plus bas : au matin du départ le créneau est fermé
        // depuis la veille à midi, et plus rien ne s'y réserve.
        $annulee = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_JOHN, adultes: 1);

        $ecrituresAvant = count($this->service(ConsulterLesPaiements::class)->pour($reservation));

        $this->horloge->nousSommesLe('2026-07-20 06:50');
        $this->service(PointerLeSolde::class)->executer($reservation);

        $this->horloge->nousSommesLe('2026-07-20 06:52');
        $this->service(PointerLeSolde::class)->annuler($reservation);
        self::assertFalse(
            $this->reservation($reservation)->estSoldee(),
            'le pointage est réversible',
        );

        $this->horloge->nousSommesLe('2026-07-20 06:55');
        $this->service(PointerLeSolde::class)->executer($reservation);
        self::assertTrue($this->reservation($reservation)->estSoldee());

        self::assertCount(
            $ecrituresAvant + 3,
            $this->service(ConsulterLesPaiements::class)->pour($reservation),
            'trois écritures conservées, et non un drapeau écrasé deux fois',
        );

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        $this->service(AnnulerCreneau::class)->executer(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MATIN,
        );

        self::assertTrue(
            $this->service(PointerLeSolde::class)->executer($annulee)->estRefuse(),
            'une réservation annulée n\'est pas pointable : il n\'y a plus rien à encaisser',
        );
    }

    /** @return list<bool> l'état du solde de chaque ligne du planning */
    private function soldesDuPlanning(): array
    {
        $lignes = $this->service(ExporterLePlanning::class)
            ->executer(Reference::JOUR_EN_SAISON)
            ->lignes();

        return array_map(static fn (array $l): bool => $l['solde_regle'], $lignes);
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
