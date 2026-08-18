<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterUneReservation;
use App\Application\CreerReservation;
use App\Domaine\ResultatDeReservation;
use App\Domaine\StatutDeReservation;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-BOOKING-01 - le formulaire de réservation en ligne.
 *
 * Aucun âge individuel n'est collecté, aucun minimum de personnes n'est imposé.
 * En revanche les coordonnées sont contrôlées, et le mobile normalisé : c'est
 * ce contrôle qui rend tenable la position du client, pour qui un message non
 * délivré relève de celui qui a mal saisi ses coordonnées.
 */
final class FormulaireDeReservationTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1 et AC-6 : une réservation pour une seule personne est acceptée.
     */
    public function test_CASE_BOOKING_20_reservation_une_seule_personne_acceptee(): void
    {
        $sortie = $this->sortie();
        $this->monde->placesVendues($sortie, Reference::TI_KAP_CAPACITE - 5);

        // La signature ne transporte aucun âge : le parcours ne peut pas en
        // demander un, c'est la règle corrigée en v3.
        $resultat = $this->creerReservation()
            ->executer($sortie, Reference::CLIENT_MARIE, adultes: 1, enfants: 0);

        self::assertTrue(
            $resultat->estAcceptee(),
            'aucun minimum de personnes n\'est imposé',
        );
        self::assertSame(
            StatutDeReservation::EN_ATTENTE_DE_PAIEMENT,
            $resultat->statut(),
        );
    }

    /**
     * AC-4, AC-7 et AC-8 : les coordonnées sont contrôlées, le champ en cause
     * est nommé, et le numéro accepté est enregistré normalisé.
     */
    public function test_CASE_BOOKING_23_coordonnees_controlees_et_numero_normalise(): void
    {
        $sortie = $this->sortie();

        $emailSansArobase = $this->creerReservation()->executer(
            $sortie,
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont-exemple',
                'telephone_mobile' => '0612345678', 'langue' => 'fr'],
            adultes: 1,
        );
        self::assertTrue($emailSansArobase->estRefusee());
        self::assertSame(
            ResultatDeReservation::MOTIF_COORDONNEES_INVALIDES,
            $emailSansArobase->motifDuRefus(),
        );
        self::assertSame(
            'email',
            $emailSansArobase->champEnCause(),
            'le refus nomme le champ en cause, pas un message générique',
        );

        $numeroFixe = $this->creerReservation()->executer(
            $sortie,
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont@example.test',
                'telephone_mobile' => '0262123456', 'langue' => 'fr'],
            adultes: 1,
        );
        self::assertTrue($numeroFixe->estRefusee());
        self::assertSame('telephone_mobile', $numeroFixe->champEnCause());

        $mobileMalEcrit = $this->creerReservation()->executer(
            $sortie,
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont@example.test',
                'telephone_mobile' => '06 12-34.56 78', 'langue' => 'fr'],
            adultes: 1,
        );
        self::assertTrue($mobileMalEcrit->estAcceptee());
        self::assertSame(
            '0612345678',
            (new ConsulterUneReservation($this->horloge))
                ->executer($mobileMalEcrit->referenceDeReservation())
                ->telephoneMobile(),
            'le numéro est enregistré sans point, tiret ni espace',
        );
    }

    private function sortie(): string
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
        return new CreerReservation($this->horloge);
    }
}
