<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ConsulterUnCode;
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
        $reservation = $this->reservationPayee(Reference::CLIENT_MARIE, adultes: 2, enfants: 1);

        $this->horloge->nousSommesLe('2026-07-20 10:00');
        $issue = $this->enregistrer($reservation, IssueDannulation::AVOIR, Reference::euros(170));

        self::assertTrue($issue->estAcceptee());
        $code = $issue->codeProduit();
        self::assertNotNull($code, 'l\'avoir est la seule issue qui produit un code');

        $vue = (new ConsulterUnCode($this->horloge))->executer($code);
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
        $premiere = $this->reservationPayee(Reference::CLIENT_MARIE, adultes: 2);
        $seconde = $this->reservationPayee(Reference::CLIENT_JOHN, adultes: 2);

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
        $reservation = $this->reservationPayee(Reference::CLIENT_MARIE, adultes: 4, enfants: 2);

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        (new MettreEnAlerte($this->horloge, $this->messages))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        // Moins de 48 heures du départ : le barème prévoirait 50 %.
        $issue = $this->enregistrer($reservation, IssueDannulation::REMBOURSEMENT, null);

        self::assertSame(
            Reference::prixDauphins(4, 2),
            $issue->montantPropose(),
            'l\'alerte l\'emporte sur le barème : le risque vient du gérant, pas du client',
        );
        self::assertTrue($issue->estAcceptee());
    }

    /** @param array{nom: string, prenom: string, email: string, telephone_mobile: string, langue: string} $client */
    private function reservationPayee(array $client, int $adultes, int $enfants = 0): string
    {
        return $this->monde->reservationPayee($this->sortie(), $client, $adultes, $enfants);
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
        ?int $montant,
    ): ResultatDissue {
        return (new EnregistrerUneIssueDannulation($this->horloge, $this->paiement))
            ->executer($reservation, $issue, $montant);
    }
}
