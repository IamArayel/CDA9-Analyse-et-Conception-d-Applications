<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterLesPlacesDisponibles;
use App\Application\ConsulterUnCreneau;
use App\Application\ConsulterUneReservation;
use App\Application\CreerReservation;
use App\Application\Tache\ControlerSeuilDeMaintien;
use App\Domaine\NaturalisteIndisponible;
use App\Domaine\ResultatDeReservation;
use App\Domaine\StatutDeReservation;
use App\Domaine\StatutDeSortie;
use App\Domaine\VueDeCreneau;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-03 - capacité, seuil minimal et places disponibles en temps réel.
 *
 * La spécification la plus exposée du projet : elle porte trois règles qui
 * coûtent de l'argent si elles cèdent, une place vendue deux fois, une sortie
 * annulée à tort, un client débité pour une place qu'il n'aura pas.
 *
 * Le seuil de maintien lui-même est une règle pure, vérifiée au niveau
 * domaine ; on ne vérifie ici que son déclenchement au contrôle des
 * 24 heures. L'affichage temps réel chez les autres clients relève du niveau
 * bout en bout et sera écrit en Gherkin.
 */
final class CapaciteEtPlacesDisponiblesTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        // Deux jours avant le créneau de référence : les réservations sont ouvertes.
        return '2026-07-18 14:00';
    }

    /**
     * AC-2 : une demande égale au nombre de places restantes est acceptée.
     */
    public function test_CASE_BOOKING_01_demande_egale_aux_places_restantes_acceptee(): void
    {
        $sortie = $this->sortieDauphinsDuTiKap();
        $this->monde->placesVendues($sortie, 9);

        $resultat = $this->creerReservation()
            ->executer($sortie, Reference::CLIENT_MARIE, adultes: 2, enfants: 1);

        self::assertTrue(
            $resultat->estAcceptee(),
            'trois places demandées pour trois restantes : la réservation passe',
        );
        self::assertSame(
            StatutDeReservation::EN_ATTENTE_DE_PAIEMENT,
            $resultat->statut(),
        );
        self::assertSame(
            0,
            $this->placesDisponibles($sortie),
            'le créneau affiche 0 place disponible aux autres clients',
        );
    }

    /**
     * AC-1 : une demande supérieure au nombre de places restantes est refusée,
     * adultes et enfants confondus.
     */
    public function test_CASE_BOOKING_02_demande_superieure_aux_places_restantes_refusee(): void
    {
        $sortie = $this->sortieDauphinsDuTiKap();
        $this->monde->placesVendues($sortie, 9);

        // Quatre places demandées, adultes et enfants comptant chacun pour une.
        $resultat = $this->creerReservation()
            ->executer($sortie, Reference::CLIENT_MARIE, adultes: 3, enfants: 1);

        self::assertTrue($resultat->estRefusee());
        self::assertSame(
            ResultatDeReservation::MOTIF_PLACES_INSUFFISANTES,
            $resultat->motifDuRefus(),
            'le motif indiqué est le nombre de places disponibles',
        );
        self::assertSame(
            3,
            $this->placesDisponibles($sortie),
            'aucune réservation créée : le créneau affiche toujours 3 places',
        );
    }

    /**
     * AC-3 et AC-8 : deux réservations concurrentes visant la dernière place ne
     * peuvent pas aboutir toutes les deux, et le second client est refusé avant
     * d'atteindre le paiement.
     */
    public function test_CASE_BOOKING_03_derniere_place_second_client_refuse_avant_paiement(): void
    {
        $sortie = $this->sortieDauphinsDuTiKap();
        $this->monde->placesVendues($sortie, 11);

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $premier = $this->creerReservation()
            ->executer($sortie, Reference::CLIENT_MARIE, adultes: 1);

        self::assertTrue($premier->estAcceptee());
        self::assertEquals(
            Reference::instant('2026-07-18 14:15'),
            $premier->immobiliseeJusquA(),
            'les places sont immobilisées 15 minutes le temps du paiement',
        );

        $this->horloge->nousSommesLe('2026-07-18 14:01');
        $encaissementsAvant = $this->paiement->nombreDencaissements();
        $second = $this->creerReservation()
            ->executer($sortie, Reference::CLIENT_JOHN, adultes: 1);

        self::assertTrue($second->estRefusee());
        self::assertSame(
            ResultatDeReservation::MOTIF_PLACES_INSUFFISANTES,
            $second->motifDuRefus(),
        );
        self::assertNull(
            $second->referenceDeReservation(),
            'aucune réservation n\'existe pour le second client',
        );
        self::assertSame(
            $encaissementsAvant,
            $this->paiement->nombreDencaissements(),
            'le refus tombe avant l\'écran de paiement : ce client n\'a pas saisi de carte',
        );
    }

    /**
     * AC-9 : passé 15 minutes sans paiement, les places immobilisées
     * redeviennent disponibles.
     */
    public function test_CASE_BOOKING_04_immobilisation_expiree_libere_les_places(): void
    {
        $sortie = $this->sortieDauphinsDuTiKap();
        $this->monde->placesVendues($sortie, 11);

        $this->horloge->nousSommesLe('2026-07-18 14:00');
        $this->monde->reservationImmobilisee($sortie, Reference::CLIENT_MARIE, adultes: 1);

        $this->horloge->nousSommesLe('2026-07-18 14:14');
        self::assertSame(
            0,
            $this->placesDisponibles($sortie),
            'avant l\'échéance, la place reste retenue même sans paiement',
        );

        // Aucune tâche planifiée n'est exécutée entre les deux lectures :
        // l'expiration s'évalue à la lecture, cf. architecture.md §5.
        $this->horloge->nousSommesLe('2026-07-18 14:16');
        self::assertSame(
            1,
            $this->placesDisponibles($sortie),
            'l\'immobilisation échue ne compte plus dans les places prises',
        );

        $repreneur = $this->creerReservation()
            ->executer($sortie, Reference::CLIENT_JOHN, adultes: 1);
        self::assertTrue(
            $repreneur->estAcceptee(),
            'un autre client peut réserver la place libérée',
        );
    }

    /**
     * AC-4 : un créneau comptant moins de 6 inscrits au contrôle des 24 heures
     * est annulé et chaque client est remboursé intégralement.
     */
    public function test_CASE_BOOKING_05_seuil_non_atteint_annule_et_rembourse(): void
    {
        $sortie = $this->sortieDauphinsDuTiKap();

        // Trois réservations, cinq participants, un de moins que le seuil.
        $marie = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $john = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_JOHN, adultes: 2);
        $karim = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_KARIM, adultes: 1);

        $this->horloge->nousSommesLe('2026-07-19 10:00');
        ($this->service(ControlerSeuilDeMaintien::class))
            ->executer();

        self::assertSame(
            StatutDeSortie::ANNULEE,
            $this->creneauDeReference()->statutDeLaSortie(Reference::TI_KAP),
        );
        self::assertSame(
            3,
            $this->paiement->nombreDeRemboursements(),
            'chacun des trois clients est remboursé',
        );
        self::assertSame(
            Reference::acompteSortie(Reference::prixDauphins(2)),
            $this->paiement->montantRembourse($marie),
            'le gérant ne rend que ce qu\'il a encaissé',
        );
        self::assertSame(
            Reference::acompteSortie(Reference::prixDauphins(2)),
            $this->paiement->montantRembourse($john),
        );
        self::assertSame(
            Reference::acompteSortie(Reference::prixDauphins(1)),
            $this->paiement->montantRembourse($karim),
        );
        self::assertFalse(
            $this->creneauDeReference()->estReservable(),
            'le créneau n\'est plus proposé à la réservation',
        );
    }

    /**
     * AC-6 : une seconde sortie baleines sur le même créneau est refusée.
     *
     * La règle vit dans un index unique en base, cf. mcd-mld.md §7 : deux
     * demandes simultanées ne peuvent pas la contourner. Ce test vérifie
     * qu'elle est traduite en refus métier et non en erreur technique.
     */
    public function test_CASE_BOOKING_07_seconde_sortie_baleines_refusee_sur_le_creneau(): void
    {
        $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_BALEINES,
        );

        $refus = null;

        try {
            $this->monde->sortieProgrammee(
                Reference::JOUR_EN_SAISON,
                Reference::CRENEAU_MILIEU_DE_MATINEE,
                Reference::LE_GRAND_BLEU,
                Reference::SORTIE_BALEINES,
            );
        } catch (NaturalisteIndisponible $indisponible) {
            $refus = $indisponible;
        }

        self::assertNotNull(
            $refus,
            'un seul naturaliste : une seconde sortie baleines est refusée sur ce créneau',
        );

        $dauphins = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::LE_GRAND_BLEU,
            Reference::SORTIE_DAUPHINS,
        );
        self::assertNotSame(
            '',
            $dauphins,
            'une sortie dauphins sur l\'autre bateau au même créneau reste acceptée',
        );
    }

    /**
     * AC-10 : une réservation dont l'acompte est versé compte dans le seuil de
     * 6 inscrits, même si son solde reste dû.
     */
    public function test_CASE_BOOKING_39_six_acomptes_maintiennent_la_sortie(): void
    {
        $sortie = $this->sortieDauphinsDuTiKap();
        $this->monde->placesVendues($sortie, 5);
        $sixieme = $this->monde->reservationConfirmee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 1,
        );

        // Ce qui distingue la v6 : le sixième inscrit compte alors qu'il doit
        // encore 70 % de sa réservation.
        self::assertGreaterThan(
            0,
            $this->service(ConsulterUneReservation::class)->executer($sixieme)->soldeDu(),
            'aucun des six n\'a réglé son solde',
        );

        $this->horloge->nousSommesLe('2026-07-19 10:00');
        $this->service(ControlerSeuilDeMaintien::class)->executer();

        self::assertSame(
            StatutDeSortie::PROGRAMMEE,
            $this->creneauDeReference()->statutDeLaSortie(Reference::TI_KAP),
            'six acomptes suffisent : le seuil compte des inscrits, pas des soldes',
        );
        self::assertTrue(
            $this->paiement->aucunRemboursementDemande(),
            'aucun remboursement : la sortie est maintenue',
        );
        self::assertSame(
            Reference::TI_KAP_CAPACITE - 6,
            $this->placesDisponibles($sortie),
            'les six places sont décomptées, bien qu\'aucune ne soit soldée',
        );
    }

    private function sortieDauphinsDuTiKap(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function creerReservation(): CreerReservation
    {
        return $this->service(CreerReservation::class);
    }

    private function placesDisponibles(string $sortie): int
    {
        return ($this->service(ConsulterLesPlacesDisponibles::class))->pour($sortie);
    }

    private function creneauDeReference(): VueDeCreneau
    {
        return ($this->service(ConsulterUnCreneau::class))->executer(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
        );
    }
}
