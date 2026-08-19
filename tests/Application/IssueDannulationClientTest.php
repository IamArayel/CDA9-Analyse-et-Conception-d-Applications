<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterUnCode;
use App\Application\EnregistrerUneAbsence;
use App\Application\EnregistrerUneIssueDannulation;
use App\Application\MettreEnAlerte;
use App\Domaine\IssueDannulation;
use App\Domaine\ResultatDissue;
use App\Tests\CasDapplication;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-ADMIN-06 - issue d'une annulation demandée par le client.
 *
 * Report, avoir ou remboursement : ce triptyque n'existe que pour une annulation
 * venue du client, jamais pour une annulation météo. C'est la correction du
 * 2026-08-14, après une transcription erronée de CR-02/Q04.
 *
 * Seul l'avoir produit un code, et c'est sa seule origine.
 */
final class IssueDannulationClientTest extends CasDapplication
{
    private ?string $sortie = null;

    protected function instantInitial(): string
    {
        return '2026-07-15 09:00';
    }

    /**
     * AC-1 et AC-2 : l'enregistrement d'un avoir produit un code unique valable
     * un an, du montant saisi par le gérant.
     */
    public function test_CASE_ADMIN_13_enregistrement_dun_avoir_produit_un_code_dun_an(): void
    {
        $reservation = $this->reservationConfirmee(Reference::CLIENT_MARIE, adultes: 2, enfants: 1);

        $this->horloge->nousSommesLe('2026-07-20 10:00');
        $issue = $this->enregistrer($reservation, IssueDannulation::AVOIR, Reference::euros(170));

        self::assertTrue($issue->estAcceptee());
        $code = $issue->codeProduit();
        self::assertNotNull($code, 'l\'avoir est la seule issue qui produit un code');

        $vue = ($this->service(ConsulterUnCode::class))->executer($code);
        self::assertSame(
            Reference::euros(170),
            $vue->montant(),
            'le code vaut le montant saisi par le gérant, pas un montant calculé',
        );
        self::assertEquals(
            Reference::instant('2027-07-20 23:59:59'),
            $vue->expireLe(),
        );
    }

    /**
     * AC-3 et AC-5 : report et remboursement ne produisent aucun code, et une
     * réservation ne porte qu'une issue.
     */
    public function test_CASE_ADMIN_14_report_et_remboursement_ne_produisent_aucun_code(): void
    {
        $premiere = $this->reservationConfirmee(Reference::CLIENT_MARIE, adultes: 2);
        $seconde = $this->reservationConfirmee(Reference::CLIENT_JOHN, adultes: 2);

        $this->horloge->nousSommesLe('2026-07-20 10:00');

        $report = $this->enregistrer($premiere, IssueDannulation::REPORT, null);
        self::assertTrue($report->estAcceptee());
        self::assertNull($report->codeProduit());

        $remboursement = $this->enregistrer(
            $seconde,
            IssueDannulation::REMBOURSEMENT,
            Reference::euros(100),
        );
        self::assertTrue($remboursement->estAcceptee());
        self::assertNull($remboursement->codeProduit());

        $seconde_issue = $this->enregistrer($premiere, IssueDannulation::AVOIR, Reference::euros(50));
        self::assertTrue($seconde_issue->estRefusee());
        self::assertSame(
            ResultatDissue::MOTIF_ISSUE_DEJA_ENREGISTREE,
            $seconde_issue->motifDuRefus(),
            'on ne rejoue pas une annulation',
        );
    }

    /**
     * AC-4 : un client qui renonce après une alerte météo est remboursé
     * intégralement, sans retenue de barème.
     */
    public function test_CASE_ADMIN_15_renoncement_apres_alerte_rembourse_integralement(): void
    {
        $reservation = $this->reservationConfirmee(Reference::CLIENT_MARIE, adultes: 4, enfants: 2);

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        ($this->service(MettreEnAlerte::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        // Moins de 48 heures du départ : le barème prévoirait 50 %.
        $issue = $this->enregistrer($reservation, IssueDannulation::REMBOURSEMENT, null);

        self::assertSame(
            Reference::acompteSortie(Reference::prixDauphins(4, 2)),
            $issue->montantPropose(),
            'l\'alerte l\'emporte sur le barème, et le versé est le plafond de ce qui peut revenir',
        );
        self::assertTrue($issue->estAcceptee());
    }

    /**
     * AC-6 et AC-7 : la retenue est plafonnée au montant versé, et la
     * différence est rendue quand la commission lui est inférieure.
     */
    public function test_CASE_ADMIN_16_retenue_plafonnee_a_lacompte(): void
    {
        $aCinqJours = $this->reservationConfirmee(Reference::CLIENT_MARIE, adultes: 2);
        $aTrenteSixHeures = $this->reservationConfirmee(Reference::CLIENT_JOHN, adultes: 2);

        $verse = Reference::acompteSortie(Reference::prixDauphins(2));

        // Cinq jours avant : la commission de 25 % vaut 25 €, moins que les 30 € versés.
        $this->horloge->nousSommesLe('2026-07-15 10:00');
        $tot = $this->enregistrer(
            $aCinqJours,
            IssueDannulation::REMBOURSEMENT,
            commission: Reference::euros(25),
        );
        self::assertSame(
            $verse - Reference::euros(25),
            $tot->montantPropose(),
            'la commission n\'épuise pas l\'acompte : 5 € reviennent au client',
        );

        // Trente-six heures avant : la commission de 50 % excède l'acompte.
        $this->horloge->nousSommesLe('2026-07-19 19:00');
        $tard = $this->enregistrer(
            $aTrenteSixHeures,
            IssueDannulation::REMBOURSEMENT,
            commission: Reference::euros(50),
        );
        self::assertSame(
            0,
            $tard->montantPropose(),
            'la retenue est plafonnée au versé : rien n\'est rendu, rien n\'est réclamé',
        );
    }

    /**
     * AC-8 : un client absent au départ est traité comme un client qui annule,
     * et perd son acompte.
     */
    public function test_CASE_ADMIN_17_client_absent_perd_son_acompte(): void
    {
        $reservation = $this->reservationConfirmee(Reference::CLIENT_MARIE, adultes: 2);

        $this->horloge->nousSommesLe('2026-07-20 07:05');
        $issue = $this->service(EnregistrerUneAbsence::class)->executer($reservation);

        self::assertTrue($issue->estAcceptee());
        self::assertSame(
            0,
            $issue->montantPropose(),
            'l\'acompte est retenu en totalité',
        );
        self::assertTrue(
            $this->paiement->aucunRemboursementDemande(),
            'et rien n\'est réclamé au client',
        );
    }

    /** @param array{nom: string, prenom: string, email: string, telephone_mobile: string, langue: string} $client */
    private function reservationConfirmee(array $client, int $adultes, int $enfants = 0): string
    {
        return $this->monde->reservationConfirmee($this->sortie(), $client, $adultes, $enfants);
    }

    /** La sortie du cas, programmée une seule fois quel que soit le nombre de clients. */
    private function sortie(): string
    {
        return $this->sortie ??= $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MATIN,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function enregistrer(
        string $reservation,
        IssueDannulation $issue,
        ?int $montant = null,
        ?int $commission = null,
    ): ResultatDissue {
        return $this->service(EnregistrerUneIssueDannulation::class)
            ->executer($reservation, $issue, $montant, $commission);
    }
}
