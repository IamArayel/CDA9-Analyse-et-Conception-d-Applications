<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\ConsulterUnCreneau;
use App\Application\MettreEnAlerte;
use App\Application\Tache\ControlerSeuilDeMaintien;
use App\Application\Tache\EnvoyerLesMessagesProgrammes;
use App\Domaine\ResultatDannulation;
use App\Domaine\StatutDeSortie;
use App\Domaine\VueDeCreneau;
use App\Tests\CasDapplication;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-CANCEL-02 - l'annulation d'un créneau est une décision du gérant.
 *
 * Aucune règle météo n'est automatisée, et l'alerte n'est pas un préalable : la
 * météo peut se dégrader en quelques heures, et imposer une alerte empêcherait
 * d'annuler un départ du matin décidé la veille au soir.
 */
final class AnnulationParLeGerantTest extends CasDapplication
{
    private const LENDEMAIN_DU_JOUR_PIVOT = '2026-07-21';

    protected function instantInitial(): string
    {
        return '2026-07-17 09:00';
    }

    /**
     * AC-1 : le gérant annule un créneau, avec ou sans alerte préalable.
     */
    public function test_CASE_CANCEL_16_annulation_avec_ou_sans_alerte_prealable(): void
    {
        $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->sortie(self::LENDEMAIN_DU_JOUR_PIVOT, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        (new MettreEnAlerte($this->horloge, $this->messages))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        self::assertTrue(
            $this->annuler(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE)->estAcceptee(),
        );
        self::assertTrue(
            $this->annuler(self::LENDEMAIN_DU_JOUR_PIVOT, Reference::CRENEAU_MILIEU_DE_MATINEE)->estAcceptee(),
            'l\'alerte préalable n\'est pas un passage obligé',
        );

        self::assertSame(
            StatutDeSortie::ANNULEE,
            $this->creneau(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE)
                ->statutDeLaSortie(Reference::TI_KAP),
        );
        self::assertSame(
            StatutDeSortie::ANNULEE,
            $this->creneau(self::LENDEMAIN_DU_JOUR_PIVOT, Reference::CRENEAU_MILIEU_DE_MATINEE)
                ->statutDeLaSortie(Reference::TI_KAP),
        );
    }

    /**
     * AC-2 et AC-3 : rien n'annule un créneau à la place du gérant, et une
     * alerte laissée sans suite vaut maintien.
     */
    public function test_CASE_CANCEL_17_aucune_annulation_sans_decision_du_gerant(): void
    {
        $sortie = $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 4);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 4);

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        (new MettreEnAlerte($this->horloge, $this->messages))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        // Huit inscrits, donc au-dessus du seuil : le contrôle des 24 heures
        // passe et ne trouve aucune raison d'annuler.
        $this->horloge->nousSommesLe('2026-07-19 07:00');
        (new ControlerSeuilDeMaintien($this->horloge, $this->paiement, $this->messages))->executer();

        $this->horloge->nousSommesLe('2026-07-20 07:00');
        (new EnvoyerLesMessagesProgrammes($this->horloge, $this->messages))->executer();

        self::assertNotSame(
            StatutDeSortie::ANNULEE,
            $this->creneau(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN)
                ->statutDeLaSortie(Reference::TI_KAP),
            'une alerte laissée sans suite n\'annule rien par expiration',
        );
        self::assertTrue(
            $this->paiement->aucunRemboursementDemande(),
            'la sortie a lieu : personne n\'est remboursé',
        );
    }

    /**
     * AC-4 et AC-5 : annuler deux fois est un geste sans effet, et une sortie
     * passée n'est plus annulable.
     */
    public function test_CASE_CANCEL_18_double_annulation_et_creneau_passe_sans_effet(): void
    {
        $creneauAVenir = $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationPayee($creneauAVenir, Reference::CLIENT_MARIE, adultes: 2);

        $creneauPasse = $this->sortie('2026-07-18', Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationPayee($creneauPasse, Reference::CLIENT_JOHN, adultes: 2);

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        $this->annuler(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->horloge->nousSommesLe('2026-07-20 08:00');
        (new EnvoyerLesMessagesProgrammes($this->horloge, $this->messages))->executer();

        $envoisApresLaPremiere = $this->messages->nombreDenvois(
            EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
        );
        $remboursementsApresLaPremiere = $this->paiement->nombreDeRemboursements();

        $seconde = $this->annuler(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
        (new EnvoyerLesMessagesProgrammes($this->horloge, $this->messages))->executer();

        self::assertTrue(
            $seconde->estSansEffet(),
            'annuler deux fois est un geste sans effet, pas une faute',
        );
        self::assertSame(
            StatutDeSortie::ANNULEE,
            $this->creneau(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE)
                ->statutDeLaSortie(Reference::TI_KAP),
        );
        self::assertSame(
            $envoisApresLaPremiere,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION),
            'aucun doublon d\'envoi',
        );
        self::assertSame(
            $remboursementsApresLaPremiere,
            $this->paiement->nombreDeRemboursements(),
            'aucun doublon de remboursement',
        );

        $surCreneauPasse = $this->annuler('2026-07-18', Reference::CRENEAU_MILIEU_DE_MATINEE);
        self::assertTrue($surCreneauPasse->estRefusee());
        self::assertSame(
            ResultatDannulation::MOTIF_CRENEAU_DEJA_PASSE,
            $surCreneauPasse->motifDuRefus(),
            'une sortie passée n\'est plus annulable : elle a eu lieu',
        );
    }

    private function sortie(string $jour, string $heure): string
    {
        return $this->monde->sortieProgrammee(
            $jour,
            $heure,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function annuler(string $jour, string $heure): ResultatDannulation
    {
        return (new AnnulerCreneau($this->horloge, $this->messages, $this->paiement))
            ->executer($jour, $heure);
    }

    private function creneau(string $jour, string $heure): VueDeCreneau
    {
        return (new ConsulterUnCreneau($this->horloge))->executer($jour, $heure);
    }
}
