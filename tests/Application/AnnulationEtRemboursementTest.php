<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\ConsulterUneReservation;
use App\Application\Tache\EnvoyerLesMessagesProgrammes;
use App\Domaine\StatutDeReservation;
use App\Domaine\VueDeReservation;
use App\Tests\CasDapplication;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-CANCEL-04 - information et remboursement des clients d'un créneau annulé.
 *
 * Depuis que le gérant ne téléphone plus (REQ-026), l'écrit est le seul canal
 * d'annonce : un message perdu n'est plus rattrapé par personne. C'est ce qui
 * fait de la trace des envois un critère d'acceptation à part entière, et non
 * un confort d'exploitation.
 *
 * Le remboursement est intégral et sans alternative : le triptyque report,
 * avoir, remboursement n'existe que pour une annulation demandée par le client.
 */
final class AnnulationEtRemboursementTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-1, AC-3 et AC-4 : chaque client est prévenu par écrit et remboursé de
     * la totalité de ce qu'il a payé.
     */
    public function test_CASE_CANCEL_10_annulation_previent_par_ecrit_et_rembourse_en_totalite(): void
    {
        $sortie = $this->sortieDuMilieuDeMatinee();
        $marie = $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $john = $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 2, enfants: 2);
        $karim = $this->monde->reservationPayee($sortie, Reference::CLIENT_KARIM, adultes: 4, enfants: 2);

        $this->annulerEtLaisserPartirLesMessages();

        self::assertSame(3, $this->paiement->nombreDeRemboursements());
        self::assertSame(Reference::prixDauphins(2), $this->paiement->montantRembourse($marie));
        self::assertSame(Reference::prixDauphins(2, 2), $this->paiement->montantRembourse($john));
        self::assertSame(Reference::prixDauphins(4, 2), $this->paiement->montantRembourse($karim));

        self::assertSame(
            6,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION),
            'trois clients sur deux canaux',
        );

        // Aucun appel téléphonique n'est nécessaire pour déclencher l'un ou
        // l'autre : il n'existe aucun canal téléphonique dans l'application.
        foreach ([Reference::CLIENT_MARIE, Reference::CLIENT_JOHN, Reference::CLIENT_KARIM] as $client) {
            self::assertSame(
                EnvoisEnregistres::LES_DEUX_CANAUX,
                $this->messages->canauxUtilises(
                    EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
                    $client['email'],
                ),
            );
        }
    }

    /**
     * AC-2 : aucun choix entre report, avoir et remboursement n'est proposé.
     *
     * La lecture antérieure venait d'une transcription erronée de CR-02/Q04,
     * rectifiée le 2026-08-14 : le triptyque n'a jamais concerné la météo.
     */
    public function test_CASE_CANCEL_11_aucun_choix_propose_apres_annulation_gerant(): void
    {
        $sortie = $this->sortieDuMilieuDeMatinee();
        $reservation = $this->monde->reservationPayee(
            $sortie,
            Reference::CLIENT_MARIE,
            adultes: 4,
            enfants: 2,
        );

        $this->annulerEtLaisserPartirLesMessages();

        self::assertSame(
            [],
            $this->reservation($reservation)->issuesProposees(),
            'ni report, ni avoir, ni choix de remboursement dans le parcours météo',
        );
        self::assertSame(
            Reference::prixDauphins(4, 2),
            $this->paiement->montantRembourse($reservation),
            'le remboursement intégral est la seule issue',
        );
    }

    /**
     * AC-5 : une réservation non payée au moment de l'annulation ne donne lieu
     * à aucun remboursement, mais son client est prévenu comme les autres.
     */
    public function test_CASE_CANCEL_12_reservation_non_payee_aucun_remboursement(): void
    {
        $sortie = $this->sortieDuMilieuDeMatinee();
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 2, enfants: 2);

        // Juste avant la fermeture des réservations du créneau de 10h, et donc
        // dans les quinze minutes qui précèdent l'annulation.
        $this->horloge->nousSommesLe('2026-07-19 11:55');
        $enAttente = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_KARIM,
            adultes: 1,
        );

        $this->horloge->nousSommesLe('2026-07-19 12:00');
        $this->annuler();

        $this->horloge->nousSommesLe('2026-07-20 08:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            2,
            $this->paiement->nombreDeRemboursements(),
            'deux remboursements sont demandés, pas trois',
        );
        foreach ($this->paiement->remboursementsDemandes() as $remboursement) {
            self::assertGreaterThan(
                0,
                $remboursement['montant'],
                'aucun montant nul n\'est envoyé au prestataire',
            );
        }

        self::assertSame(
            StatutDeReservation::ANNULEE,
            $this->reservation($enAttente)->statut(),
            'l\'immobilisation de la troisième est libérée',
        );
        self::assertSame(
            EnvoisEnregistres::LES_DEUX_CANAUX,
            $this->messages->canauxUtilises(
                EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
                Reference::CLIENT_KARIM['email'],
            ),
            'il est inscrit, même s\'il n\'a rien versé : il est prévenu comme les autres',
        );
    }

    /**
     * AC-6 : la date et le canal de chaque message envoyé sont enregistrés,
     * échec compris.
     *
     * Sans cette trace, le gérant ne peut rien répondre à un client affirmant
     * n'avoir rien reçu.
     */
    public function test_CASE_CANCEL_13_trace_des_envois_type_canal_et_date(): void
    {
        $sortie = $this->sortieDuMilieuDeMatinee();
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 2, enfants: 2);

        // L'adresse de ce client est invalide : son e-mail partira en échec.
        $this->messages->feraEchouer(
            EnvoisEnregistres::CANAL_EMAIL,
            Reference::CLIENT_JOHN['email'],
        );

        $this->annulerEtLaisserPartirLesMessages();

        self::assertSame(
            4,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION),
            'quatre traces, pas deux : le canal fait partie de la trace',
        );

        foreach ($this->messages->envois(EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION) as $envoi) {
            self::assertEquals(
                Reference::instant('2026-07-20 08:00'),
                $envoi['envoyeLe'],
                'chaque trace porte sa date',
            );
        }

        self::assertSame(
            EnvoisEnregistres::STATUT_ECHEC,
            $this->messages->statutDenvoi(
                EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
                EnvoisEnregistres::CANAL_EMAIL,
                Reference::CLIENT_JOHN['email'],
            ),
            'l\'envoi e-mail en échec est enregistré comme tel',
        );
        self::assertSame(
            EnvoisEnregistres::STATUT_ENVOYE,
            $this->messages->statutDenvoi(
                EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
                EnvoisEnregistres::CANAL_SMS,
                Reference::CLIENT_JOHN['email'],
            ),
            'l\'échec d\'un canal n\'empêche pas l\'autre de partir',
        );
        self::assertCount(1, $this->messages->envoisEnEchec());
    }

    private function sortieDuMilieuDeMatinee(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    /** Le gérant annule, puis les messages partent à leur heure, 2 h avant. */
    private function annulerEtLaisserPartirLesMessages(): void
    {
        $this->horloge->nousSommesLe('2026-07-20 07:00');
        $this->annuler();

        $this->horloge->nousSommesLe('2026-07-20 08:00');
        $this->envoyerLesMessagesProgrammes();
    }

    private function annuler(): void
    {
        (new AnnulerCreneau($this->horloge, $this->messages, $this->paiement))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
    }

    private function envoyerLesMessagesProgrammes(): void
    {
        (new EnvoyerLesMessagesProgrammes($this->horloge, $this->messages))->executer();
    }

    private function reservation(string $reference): VueDeReservation
    {
        return (new ConsulterUneReservation($this->horloge))->executer($reference);
    }
}
