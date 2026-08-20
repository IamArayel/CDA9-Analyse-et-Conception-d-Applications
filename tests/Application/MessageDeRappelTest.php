<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\Tache\EnvoyerLesMessagesProgrammes;
use App\Tests\CasDapplication;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-CANCEL-05 - message de rappel avant la sortie.
 *
 * Le rappel part seul, sans action du gérant, et porte la prévision météo qu'il
 * a saisie : l'application n'interroge aucun service externe. Le délai est lu
 * au moment de l'envoi, jamais figé à la confirmation de la réservation.
 */
final class MessageDeRappelTest extends CasDapplication
{
    private const LENDEMAIN_DU_JOUR_PIVOT = '2026-07-21';
    private const PREVISION = 'mer peu agitée, prévoir un coupe-vent';

    protected function instantInitial(): string
    {
        return '2026-07-17 09:00';
    }

    /**
     * AC-1 et AC-2 : le rappel part 24 heures avant le départ, sur les deux
     * canaux, et porte la prévision saisie par le gérant.
     */
    public function test_CASE_CANCEL_21_rappel_24h_avant_sur_les_deux_canaux(): void
    {
        $sortie = $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_JOHN, adultes: 1);
        $this->monde->previsionMeteo(Reference::JOUR_EN_SAISON, self::PREVISION);

        $this->horloge->nousSommesLe('2026-07-19 09:59');
        $this->envoyerLesMessagesProgrammes();
        self::assertSame(
            0,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_RAPPEL),
            'la valeur par défaut est bien 24 heures avant le départ',
        );

        $this->horloge->nousSommesLe('2026-07-19 10:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            4,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_RAPPEL),
            'deux clients sur deux canaux, sans aucune action du gérant',
        );
        foreach ([Reference::CLIENT_MARIE, Reference::CLIENT_JOHN] as $client) {
            self::assertSame(
                EnvoisEnregistres::LES_DEUX_CANAUX,
                $this->messages->canauxUtilises(EnvoisEnregistres::TYPE_RAPPEL, $client['email']),
            );
        }
        self::assertSame(
            self::PREVISION,
            $this->messages->donneesDenvoi(
                EnvoisEnregistres::TYPE_RAPPEL,
                EnvoisEnregistres::CANAL_EMAIL,
                Reference::CLIENT_MARIE['email'],
            )['prevision_meteo'] ?? null,
            'la prévision vient de la saisie du gérant, pas d\'un service externe',
        );
    }

    /**
     * AC-3 : l'horaire de rappel modifié s'applique aux envois à venir, et un
     * rappel déjà parti n'est pas rejoué.
     */
    public function test_CASE_CANCEL_22_horaire_de_rappel_modifie_applique_aux_envois_a_venir(): void
    {
        $sortieProche = $this->sortie('2026-07-19', Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationConfirmee($sortieProche, Reference::CLIENT_MARIE, adultes: 1);

        $sortieLointaine = $this->sortie(self::LENDEMAIN_DU_JOUR_PIVOT, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationConfirmee($sortieLointaine, Reference::CLIENT_JOHN, adultes: 1);

        // Le rappel de la sortie proche part sous le délai par défaut.
        $this->horloge->nousSommesLe('2026-07-18 10:00');
        $this->envoyerLesMessagesProgrammes();
        self::assertSame(2, $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_RAPPEL));

        $this->horloge->nousSommesLe('2026-07-18 11:00');
        $this->monde->parametresDenvoi(delaiDeRappelEnHeures: 48);

        $this->horloge->nousSommesLe('2026-07-19 10:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            4,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_RAPPEL),
            'le rappel de la sortie du 21 part 48 heures avant, et celui du 19 n\'est pas rejoué',
        );
        self::assertEquals(
            Reference::instant('2026-07-19 10:00'),
            $this->messages->dateDenvoi(
                EnvoisEnregistres::TYPE_RAPPEL,
                EnvoisEnregistres::CANAL_EMAIL,
                Reference::CLIENT_JOHN['email'],
            ),
            'le délai est lu au moment de l\'envoi, pas figé à la confirmation',
        );
    }

    /**
     * AC-5 : une réservation prise après l'horaire de rappel déclenche le
     * rappel immédiatement, et non pas jamais.
     */
    public function test_CASE_CANCEL_23_reservation_tardive_declenche_le_rappel_immediatement(): void
    {
        $sortie = $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);
        $this->monde->previsionMeteo(Reference::JOUR_EN_SAISON, self::PREVISION);

        // L'horaire de rappel de ce départ, le 19 à 07h00, est déjà passé ; les
        // réservations, elles, restent ouvertes jusqu'à midi la veille.
        $this->horloge->nousSommesLe('2026-07-19 11:00');
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_KARIM, adultes: 2);

        self::assertSame(
            2,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_RAPPEL),
            'le client reçoit son rappel bien qu\'il ait réservé après l\'horaire programmé',
        );
        self::assertEquals(
            Reference::instant('2026-07-19 11:00'),
            $this->messages->dateDenvoi(
                EnvoisEnregistres::TYPE_RAPPEL,
                EnvoisEnregistres::CANAL_SMS,
                Reference::CLIENT_KARIM['email'],
            ),
        );
    }

    /**
     * AC-4 et AC-6 : aucun rappel pour un créneau annulé, et l'échec d'un canal
     * n'emporte pas l'autre.
     */
    public function test_CASE_CANCEL_24_aucun_rappel_si_annule_et_echec_dun_canal_isole(): void
    {
        $annule = $this->sortie(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationConfirmee($annule, Reference::CLIENT_MARIE, adultes: 2);

        $maintenu = $this->sortie(self::LENDEMAIN_DU_JOUR_PIVOT, Reference::CRENEAU_MILIEU_DE_MATINEE);
        $this->monde->reservationConfirmee($maintenu, Reference::CLIENT_JOHN, adultes: 2);
        $this->messages->feraEchouer(
            EnvoisEnregistres::CANAL_EMAIL,
            Reference::CLIENT_JOHN['email'],
        );

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        ($this->service(AnnulerCreneau::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->horloge->nousSommesLe('2026-07-19 10:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            [],
            $this->messages->envois(
                EnvoisEnregistres::TYPE_RAPPEL,
                destinataire: Reference::CLIENT_MARIE['email'],
            ),
            'zéro rappel pour le créneau annulé : ses clients ont déjà leur message d\'annulation',
        );

        $this->horloge->nousSommesLe('2026-07-20 10:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            EnvoisEnregistres::STATUT_ENVOYE,
            $this->messages->statutDenvoi(
                EnvoisEnregistres::TYPE_RAPPEL,
                EnvoisEnregistres::CANAL_SMS,
                Reference::CLIENT_JOHN['email'],
            ),
            'le SMS part quand même',
        );
        self::assertSame(
            EnvoisEnregistres::STATUT_ECHEC,
            $this->messages->statutDenvoi(
                EnvoisEnregistres::TYPE_RAPPEL,
                EnvoisEnregistres::CANAL_EMAIL,
                Reference::CLIENT_JOHN['email'],
            ),
            'un envoi réussi et un échec enregistré, pas un silence complet',
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

    private function envoyerLesMessagesProgrammes(): void
    {
        ($this->service(EnvoyerLesMessagesProgrammes::class))->executer();
    }
}
