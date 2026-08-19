<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterUnCreneau;
use App\Application\MettreEnAlerte;
use App\Domaine\StatutDeSortie;
use App\Domaine\VueDeCreneau;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-CANCEL-01 - consultation d'un créneau depuis l'espace de gestion.
 *
 * Consulter, c'est regarder : la consultation ne déclenche ni alerte, ni
 * annulation, ni message. C'est la garantie sans laquelle un gérant hésiterait
 * à ouvrir un créneau pour se faire une idée.
 */
final class ConsultationDunCreneauTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1 et AC-2 : la consultation affiche les inscrits, et elle seule.
     */
    public function test_CASE_CANCEL_14_consultation_affiche_les_inscrits_sans_effet_de_bord(): void
    {
        $sortie = $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 2, enfants: 2);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_KARIM, adultes: 1);
        $this->monde->reservationImmobilisee($sortie, ['nom' => 'Nguyen', 'prenom' => 'Lan',
            'email' => 'lan.nguyen@example.test', 'telephone_mobile' => '0692000004', 'langue' => 'fr'],
            adultes: 1);

        $vue = $this->creneau(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $inscrits = $vue->inscrits(Reference::TI_KAP);

        self::assertCount(
            3,
            $inscrits,
            'le client qui n\'a pas payé n\'est pas un inscrit',
        );
        foreach ($inscrits as $inscrit) {
            self::assertArrayHasKey('nom', $inscrit);
            self::assertArrayHasKey('email', $inscrit);
            self::assertArrayHasKey('telephone_mobile', $inscrit);
            self::assertArrayHasKey('participants', $inscrit);
        }
        self::assertSame(
            [2, 4, 1],
            array_column($inscrits, 'participants'),
            'chaque ligne porte le nombre de participants de sa réservation',
        );

        self::assertSame(
            StatutDeSortie::PROGRAMMEE,
            $vue->statutDeLaSortie(Reference::TI_KAP),
            'la consultation ne déclenche ni alerte ni annulation',
        );
        self::assertTrue(
            $this->messages->aucunEnvoi(),
            'et aucun message : elle est sans effet de bord',
        );
    }

    /**
     * AC-3 et AC-4 : un créneau vide reste annulable, et un créneau en alerte
     * affiche la date de son alerte.
     */
    public function test_CASE_CANCEL_15_creneau_vide_annulable_et_alerte_datee(): void
    {
        // Hors saison, un créneau sans inscrit est un résultat normal.
        $this->sortie('2027-02-01', Reference::CRENEAU_MILIEU_DE_MATINEE);
        $creneauVide = $this->creneau('2027-02-01', Reference::CRENEAU_MILIEU_DE_MATINEE);

        self::assertSame([], $creneauVide->inscrits(Reference::TI_KAP));
        self::assertTrue(
            $creneauVide->estAnnulable(),
            'une liste vide n\'est pas une erreur, et n\'empêche pas d\'annuler',
        );

        $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);
        $this->horloge->nousSommesLe('2026-07-19 09:00');
        ($this->service(MettreEnAlerte::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        self::assertEquals(
            Reference::instant('2026-07-19 09:00'),
            $this->creneau(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN)
                ->dateDeMiseEnAlerte(Reference::TI_KAP),
            'la date d\'alerte évite au gérant d\'en poser une seconde',
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

    private function creneau(string $jour, string $heure): VueDeCreneau
    {
        return ($this->service(ConsulterUnCreneau::class))->executer($jour, $heure);
    }
}
