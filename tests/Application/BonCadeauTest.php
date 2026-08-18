<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AppliquerUnCode;
use App\Application\ConfirmerLePaiement;
use App\Application\ConsulterUnCode;
use App\Application\ConsulterUneReservation;
use App\Domaine\ResultatDapplicationDunCode;
use App\Domaine\StatutDeReservation;
use App\Domaine\VueDeCode;
use App\Domaine\VueDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-09 - bons cadeaux.
 *
 * Un bon vaut un montant, pas une sortie : depuis la v4 il ne porte ni type de
 * sortie ni catégorie de passager. Il se consomme en une fois, quel que soit le
 * reliquat, et ne se cumule avec aucun autre code.
 *
 * La validité dans le temps est une règle pure, vérifiée au niveau domaine.
 */
final class BonCadeauTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-01 09:00';
    }

    /**
     * AC-1 et AC-7 : l'achat délivre un code unique, valable un an, et sans
     * aucun rattachement à un type de sortie.
     */
    public function test_CASE_BOOKING_14_achat_bon_cadeau_delivre_un_code_unique_dun_an(): void
    {
        // La commande ne demande qu'un montant : la signature de l'achat ne
        // transporte aucun type de sortie, c'est la règle inversée en v4.
        $code = $this->monde->bonCadeauAchete(Reference::euros(150), '2026-07-20');

        $bon = $this->code($code);
        self::assertSame(Reference::euros(150), $bon->montant());
        self::assertEquals(
            Reference::instant('2027-07-20 23:59:59'),
            $bon->expireLe(),
            'l\'expiration tombe à un an jour pour jour de l\'achat',
        );
        self::assertTrue($bon->estUtilisable());

        $second = $this->monde->bonCadeauAchete(Reference::euros(150), '2026-07-20');
        self::assertNotSame($code, $second, 'chaque achat délivre un code distinct');
    }

    /**
     * AC-2 et AC-3 : un bon insuffisant est déduit du total, et la différence
     * reste à payer par carte.
     */
    public function test_CASE_BOOKING_15_bon_cadeau_insuffisant_solde_paye_par_carte(): void
    {
        $sortie = $this->sortieBaleines();
        $code = $this->monde->bonCadeauAchete(Reference::euros(100), '2026-07-01');

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 2,
            enfants: 1,
        );

        $application = (new AppliquerUnCode())->executer($reservation, $code);

        self::assertTrue($application->estAccepte());
        self::assertSame(
            Reference::prixBaleines(2, 1) - Reference::euros(100),
            $application->montantRestantDu(),
            '170 € moins 100 € : 70 € restent à payer par carte',
        );

        $encaissementsAvant = $this->paiement->nombreDencaissements();
        (new ConfirmerLePaiement($this->horloge, $this->paiement))->executer($reservation);

        self::assertSame(
            $encaissementsAvant + 1,
            $this->paiement->nombreDencaissements(),
        );
        self::assertSame(
            Reference::euros(70),
            $this->paiement->montantEncaisse($reservation),
            'le montant demandé au prestataire est 70 €, pas 170 €',
        );
        self::assertFalse(
            $this->code($code)->estUtilisable(),
            'le code est marqué utilisé',
        );
        self::assertSame(
            StatutDeReservation::CONFIRMEE,
            $this->reservation($reservation)->statut(),
        );
    }

    /**
     * AC-4 : le surplus d'un bon supérieur au prix est perdu.
     *
     * La règle est défavorable au bénéficiaire, et c'est celle que le client a
     * posée deux fois.
     */
    public function test_CASE_BOOKING_16_surplus_du_bon_cadeau_est_perdu(): void
    {
        $sortie = $this->sortieDauphins();
        $code = $this->monde->bonCadeauAchete(Reference::euros(150), '2026-07-01');

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 1,
            enfants: 1,
        );

        $application = (new AppliquerUnCode())->executer($reservation, $code);

        self::assertSame(
            0,
            $application->montantRestantDu(),
            'un bon de 150 € couvre une réservation de 80 €',
        );

        (new ConfirmerLePaiement($this->horloge, $this->paiement))->executer($reservation);

        self::assertSame(
            StatutDeReservation::CONFIRMEE,
            $this->reservation($reservation)->statut(),
        );
        self::assertNull(
            $this->reservation($reservation)->avoirProduit(),
            'aucun avoir n\'est produit pour les 70 € non consommés',
        );
        self::assertTrue(
            $this->paiement->aucunRemboursementDemande(),
            'ni avoir, ni remboursement : le surplus est perdu',
        );
        self::assertFalse(
            $this->code($code)->estUtilisable(),
            'le bon est marqué utilisé, sans reliquat',
        );
    }

    /**
     * AC-5 : un bon déjà utilisé est refusé.
     *
     * Le message de refus ne distingue pas un code déjà utilisé d'un code
     * inexistant : sans quoi la différence permettrait de sonder les codes.
     */
    public function test_CASE_BOOKING_17_bon_cadeau_deja_utilise_refuse(): void
    {
        $sortiePremiere = $this->sortieDauphins();
        $code = $this->monde->bonCadeauDejaUtilise(
            Reference::euros(100),
            '2026-07-01',
            $sortiePremiere,
        );

        $sortieSeconde = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_BALEINES,
        );

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $reservation = $this->monde->reservationImmobilisee(
            $sortieSeconde,
            Reference::CLIENT_JOHN,
            adultes: 2,
        );

        $refus = (new AppliquerUnCode())->executer($reservation, $code);

        self::assertTrue($refus->estRefuse());
        self::assertSame(
            Reference::prixBaleines(2),
            $refus->montantRestantDu(),
            'aucune déduction n\'est appliquée : les 130 € restent dus',
        );

        $codeInexistant = (new AppliquerUnCode())->executer($reservation, 'CODE-QUI-NEXISTE-PAS');
        self::assertSame(
            $codeInexistant->motifDuRefus(),
            $refus->motifDuRefus(),
            'un code déjà utilisé et un code inexistant donnent le même refus',
        );
        self::assertSame(
            ResultatDapplicationDunCode::MOTIF_CODE_INVALIDE,
            $refus->motifDuRefus(),
        );
    }

    /**
     * SPEC-BOOKING-09 AC-8 et SPEC-BOOKING-10 AC-5 : un bon cadeau et un code
     * d'avoir ne se cumulent pas.
     *
     * Le non-cumul est porté par une contrainte de la base, pas seulement par
     * le code applicatif, cf. mcd-mld.md §7.
     */
    public function test_CASE_BOOKING_19_non_cumul_bon_cadeau_et_avoir(): void
    {
        $sortie = $this->sortieBaleines();
        $bon = $this->monde->bonCadeauAchete(Reference::euros(100), '2026-07-01');
        $avoir = $this->monde->avoirEmis(Reference::euros(50), '2026-07-01');

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 2,
            enfants: 1,
        );

        $premier = (new AppliquerUnCode())->executer($reservation, $bon);
        self::assertTrue($premier->estAccepte());
        self::assertSame(Reference::euros(70), $premier->montantRestantDu());

        $second = (new AppliquerUnCode())->executer($reservation, $avoir);

        self::assertTrue($second->estRefuse());
        self::assertSame(
            ResultatDapplicationDunCode::MOTIF_CODES_NON_CUMULABLES,
            $second->motifDuRefus(),
        );
        self::assertSame(
            Reference::euros(70),
            $second->montantRestantDu(),
            'le montant restant dû ne bouge pas',
        );
        self::assertTrue(
            $this->code($avoir)->estUtilisable(),
            'le second code n\'est pas consommé : il reste utilisable ailleurs',
        );
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

    private function code(string $code): VueDeCode
    {
        return (new ConsulterUnCode($this->horloge))->executer($code);
    }

    private function reservation(string $reference): VueDeReservation
    {
        return (new ConsulterUneReservation($this->horloge))->executer($reference);
    }
}
