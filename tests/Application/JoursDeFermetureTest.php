<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AjouterUnJourDeFermeture;
use App\Application\ConsulterLeCalendrier;
use App\Application\ConsulterLesJoursDeFermeture;
use App\Application\ConsulterUneReservation;
use App\Application\RetirerUnJourDeFermeture;
use App\Domaine\StatutDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-ADMIN-04 - jours de fermeture.
 *
 * Les deux jours fériés d'usage sont présents sans que personne les ait saisis.
 * Fermer une date déjà réservée est accepté, mais n'annule ni ne rembourse
 * rien : c'est l'effet de bord relevé dans l'analyse d'impact, que le client
 * n'avait pas envisagé.
 */
final class JoursDeFermetureTest extends CasDapplication
{
    private const QUINZE_AOUT = '2026-08-15';

    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1, AC-2 et AC-3 : les deux jours de fermeture par défaut sont là, et
     * la liste est modifiable dans les deux sens.
     */
    public function test_CASE_ADMIN_08_jours_de_fermeture_par_defaut_et_modifiables(): void
    {
        self::assertSame(
            Reference::JOURS_DE_FERMETURE,
            (new ConsulterLesJoursDeFermeture($this->horloge))->executer(),
            'le 25 décembre et le 1er janvier sont présents sans saisie',
        );

        (new AjouterUnJourDeFermeture($this->horloge))->executer(self::QUINZE_AOUT);
        self::assertSame(
            [],
            $this->creneauxProposes(self::QUINZE_AOUT),
            'l\'ajout prend effet le jour même de l\'enregistrement',
        );

        (new RetirerUnJourDeFermeture($this->horloge))->executer('2026-12-25');
        self::assertCount(
            3,
            $this->creneauxProposes('2026-12-25'),
            'les trois créneaux sont de nouveau proposés',
        );
    }

    /**
     * AC-4 : fermer une date déjà réservée est accepté, liste les réservations
     * concernées, et n'annule ni ne rembourse rien.
     */
    public function test_CASE_ADMIN_09_fermeture_dune_date_reservee_nannule_rien(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            self::QUINZE_AOUT,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $marie = $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $john = $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 2);

        $resultat = (new AjouterUnJourDeFermeture($this->horloge))->executer(self::QUINZE_AOUT);

        self::assertTrue($resultat->estAcceptee());
        self::assertCount(
            2,
            $resultat->reservationsConcernees(),
            'le gérant est averti : à lui de traiter ces clients',
        );

        foreach ([$marie, $john] as $reservation) {
            self::assertSame(
                StatutDeReservation::CONFIRMEE,
                (new ConsulterUneReservation($this->horloge))->executer($reservation)->statut(),
                'aucune réservation n\'est annulée automatiquement',
            );
        }
        self::assertTrue(
            $this->paiement->aucunRemboursementDemande(),
            'aucun appel au prestataire de paiement',
        );
    }

    /** @return list<string> */
    private function creneauxProposes(string $jour): array
    {
        return (new ConsulterLeCalendrier($this->horloge))->executer($jour)->creneauxProposes();
    }
}
