<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AppliquerUnCode;
use App\Application\ConfirmerLePaiement;
use App\Application\SolderUneReservation;
use App\Application\Tache\EnvoyerLesMessagesProgrammes;
use App\Domaine\ResultatDePaiement;
use App\Tests\CasDapplication;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-CANCEL-07 - le lien de règlement du solde.
 *
 * Le quatrième message automatique. Il se distingue des trois autres sur deux
 * points que ce test vérifie : son heure ne dépend pas du créneau, et il ne part
 * que par courriel.
 *
 * Le créneau retenu est celui de **14h**, le seul où 7h la veille ne se confond
 * pas avec 24 heures avant le départ.
 */
final class LienDeReglementTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 09:00';
    }

    /**
     * AC-1 à AC-3, limites 2 et 4 : le lien part à 7h la veille, par courriel
     * seul, à qui doit encore quelque chose, et une seule fois.
     */
    public function test_CASE_CANCEL_25_lien_de_reglement_envoye_a_sept_heures_la_veille(): void
    {
        $sortie = $this->sortieDeLapresMidi();
        $marie = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $code = $this->monde->bonCadeauAchete(Reference::euros(150), '2026-07-18');
        $john = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_JOHN,
            adultes: 1,
            enfants: 1,
        );
        $this->service(AppliquerUnCode::class)->executer($john, $code);
        $this->service(ConfirmerLePaiement::class)->executer($john);

        $this->horloge->nousSommesLe('2026-07-19 06:59');
        $this->envoyerLesMessagesProgrammes();
        self::assertSame(
            0,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_LIEN_DE_REGLEMENT),
            '7h la veille, et non 24 heures avant un départ de 14h',
        );

        $this->horloge->nousSommesLe('2026-07-19 07:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            [EnvoisEnregistres::CANAL_EMAIL],
            $this->messages->canauxUtilises(
                EnvoisEnregistres::TYPE_LIEN_DE_REGLEMENT,
                Reference::CLIENT_MARIE['email'],
            ),
            'par courriel seul : un lien de paiement dans un SMS inviterait au hameçonnage',
        );
        self::assertNotContains(
            Reference::CLIENT_JOHN['email'],
            $this->messages->destinataires(EnvoisEnregistres::TYPE_LIEN_DE_REGLEMENT),
            'son bon cadeau a tout couvert : il n\'a aucun solde à régler',
        );
        self::assertTrue(
            $this->service(SolderUneReservation::class)->executer($marie)->estConfirme(),
            'la fenêtre de règlement s\'ouvre exactement quand le lien part',
        );

        $this->horloge->nousSommesLe('2026-07-19 08:00');
        $this->envoyerLesMessagesProgrammes();
        self::assertSame(
            1,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_LIEN_DE_REGLEMENT),
            'la tâche repasse, le lien ne se rejoue pas',
        );
    }

    /**
     * Limite 1 : une réservation prise après 7h la veille reçoit son lien dès
     * l'encaissement de l'acompte, et non pas jamais.
     */
    public function test_CASE_CANCEL_25_lien_envoye_des_lacompte_pour_une_reservation_tardive(): void
    {
        $sortie = $this->sortieDeLapresMidi();

        $this->horloge->nousSommesLe('2026-07-19 11:00');
        $tardive = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_KARIM, adultes: 1);

        self::assertContains(
            Reference::CLIENT_KARIM['email'],
            $this->messages->destinataires(EnvoisEnregistres::TYPE_LIEN_DE_REGLEMENT),
            'sans ce second chemin, ce client n\'aurait jamais de lien',
        );
        self::assertNotSame(
            ResultatDePaiement::MOTIF_HORS_FENETRE,
            $this->service(SolderUneReservation::class)->executer($tardive)->motifDuRefus(),
        );
    }

    private function sortieDeLapresMidi(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
    }

    private function envoyerLesMessagesProgrammes(): void
    {
        $this->service(EnvoyerLesMessagesProgrammes::class)->executer();
    }
}
