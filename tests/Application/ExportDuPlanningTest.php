<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ExporterLePlanning;
use App\Domaine\DocumentImprimable;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-ADMIN-03 - export imprimable du planning.
 *
 * Le planning liste ce qui embarque, pas ce qui est en cours d'achat. Et une
 * journée vide s'imprime : hors saison, l'absence de réservation est un
 * résultat métier normal, pas une erreur de l'outil.
 */
final class ExportDuPlanningTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1, AC-2 et AC-4 : l'export produit un document imprimable, groupé par
     * créneau dans l'ordre chronologique, sans les réservations non payées.
     */
    public function test_CASE_ADMIN_06_export_du_planning_groupe_par_creneau(): void
    {
        $matin = $this->sortie(Reference::CRENEAU_MATIN);
        $milieu = $this->sortie(Reference::CRENEAU_MILIEU_DE_MATINEE);
        $apresMidi = $this->sortie(Reference::CRENEAU_APRES_MIDI);

        $this->monde->reservationConfirmee($matin, Reference::CLIENT_MARIE, adultes: 2);
        $this->monde->reservationConfirmee($matin, Reference::CLIENT_JOHN, adultes: 1);
        $this->monde->reservationConfirmee($milieu, Reference::CLIENT_KARIM, adultes: 2);

        $this->horloge->nousSommesLe('2026-07-20 11:00');
        $this->monde->reservationImmobilisee($apresMidi, ['nom' => 'Nguyen', 'prenom' => 'Lan',
            'email' => 'lan.nguyen@example.test', 'telephone_mobile' => '0692000004', 'langue' => 'fr'],
            adultes: 1);

        $document = $this->exporter(Reference::JOUR_EN_SAISON);

        self::assertTrue($document->estUnPdf());
        self::assertSame(
            [Reference::CRENEAU_MATIN, Reference::CRENEAU_MILIEU_DE_MATINEE],
            $document->creneaux(),
            'regroupées par créneau, dans l\'ordre chronologique',
        );
        self::assertCount(
            3,
            $document->lignes(),
            'les trois réservations payées, et elles seules',
        );
        self::assertNotContains(
            'lan.nguyen@example.test',
            array_column($document->lignes(), 'email'),
            'le planning liste ce qui embarque, pas ce qui est en cours d\'achat',
        );
    }

    /**
     * AC-3 : une période sans réservation produit un document lisible, qui
     * indique explicitement l'absence de réservation.
     */
    public function test_CASE_ADMIN_07_periode_sans_reservation_produit_un_document_lisible(): void
    {
        $document = $this->exporter('2027-02-01');

        self::assertTrue($document->estUnPdf());
        self::assertSame([], $document->lignes());
        self::assertTrue(
            $document->mentionneLabsenceDeReservation(),
            'le gérant doit pouvoir imprimer une journée vide sans se demander si l\'outil a échoué',
        );
    }

    private function sortie(string $heure): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            $heure,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function exporter(string $jour): DocumentImprimable
    {
        return ($this->service(ExporterLePlanning::class))->executer($jour);
    }
}
