<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\ConsulterUnCreneau;
use App\Application\MettreEnAlerte;
use App\Application\Tache\EnvoyerLesMessagesProgrammes;
use App\Domaine\StatutDeSortie;
use App\Domaine\VueDeCreneau;
use App\Tests\CasDapplication;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-CANCEL-06 - alerte météo préventive.
 *
 * Le différenciant du projet, et la spécification la plus étendue du domaine.
 * Le gérant place un créneau en alerte ; les clients inscrits en sont avertis
 * la veille à 18h et ne reçoivent un second message que si la sortie est
 * finalement annulée, deux heures avant le départ. Le silence vaut maintien :
 * c'est la règle que le client a posée deux fois.
 *
 * Rien ne s'y déclenche seul, sauf les envois programmés. C'est donc l'horloge
 * injectée qui commande, jamais l'heure système (ADR-005, option B).
 */
final class AlerteMeteoTest extends CasDapplication
{
    private const LENDEMAIN_DU_JOUR_PIVOT = '2026-07-21';

    protected function instantInitial(): string
    {
        // La veille du jour pivot, au matin : le gérant est à son bureau.
        return '2026-07-19 09:00';
    }

    /**
     * AC-1 et AC-10 : le gérant met en alerte un créneau donné sans affecter
     * les autres créneaux du jour, et l'alerte couvre les deux bateaux du
     * créneau, la météo ne les distinguant pas.
     */
    public function test_CASE_CANCEL_01_alerte_couvre_les_deux_bateaux_du_creneau(): void
    {
        $this->journeeDeReference();

        $this->mettreEnAlerte(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $creneauEnAlerte = $this->creneau(Reference::CRENEAU_MILIEU_DE_MATINEE);
        self::assertSame(
            StatutDeSortie::EN_ALERTE,
            $creneauEnAlerte->statutDeLaSortie(Reference::TI_KAP),
        );
        self::assertSame(
            StatutDeSortie::EN_ALERTE,
            $creneauEnAlerte->statutDeLaSortie(Reference::LE_GRAND_BLEU),
        );
        self::assertEquals(
            $creneauEnAlerte->dateDeMiseEnAlerte(Reference::TI_KAP),
            $creneauEnAlerte->dateDeMiseEnAlerte(Reference::LE_GRAND_BLEU),
            'les deux bateaux portent la même date de mise en alerte',
        );

        self::assertSame(
            StatutDeSortie::PROGRAMMEE,
            $this->creneau(Reference::CRENEAU_MATIN)->statutDeLaSortie(Reference::TI_KAP),
            'le créneau de 7h n\'a pas bougé',
        );
        self::assertSame(
            StatutDeSortie::PROGRAMMEE,
            $this->creneau(Reference::CRENEAU_APRES_MIDI)->statutDeLaSortie(Reference::TI_KAP),
            'le créneau de 14h n\'a pas bougé',
        );
    }

    /**
     * AC-2 : aucune alerte n'est déclenchée sans action du gérant.
     *
     * L'application n'interroge aucun service météo, et il n'existe aucun port
     * pour en interroger un : la décision appartient au gérant, et à lui seul.
     */
    public function test_CASE_CANCEL_02_aucune_alerte_sans_action_du_gerant(): void
    {
        $this->journeeDeReference();

        $this->horloge->nousSommesLe('2026-07-19 18:00');
        $this->envoyerLesMessagesProgrammes();

        foreach ([Reference::CRENEAU_MATIN, Reference::CRENEAU_MILIEU_DE_MATINEE, Reference::CRENEAU_APRES_MIDI] as $heure) {
            self::assertSame(
                StatutDeSortie::PROGRAMMEE,
                $this->creneau($heure)->statutDeLaSortie(Reference::TI_KAP),
            );
        }

        self::assertSame(
            0,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_ALERTE_METEO),
            'aucun message d\'alerte, quel que soit le canal',
        );
    }

    /**
     * AC-3 : les clients inscrits reçoivent le message d'alerte la veille à
     * 18h, par SMS et par e-mail.
     */
    public function test_CASE_CANCEL_03_message_alerte_la_veille_a_18h_sur_deux_canaux(): void
    {
        $sortie = $this->sortieDuMatin();
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 1);

        $this->mettreEnAlerte(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        $this->horloge->nousSommesLe('2026-07-19 17:59');
        $this->envoyerLesMessagesProgrammes();
        self::assertSame(
            0,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_ALERTE_METEO),
            'rien ne part avant l\'heure programmée',
        );

        $this->horloge->nousSommesLe('2026-07-19 18:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            4,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_ALERTE_METEO),
            'deux clients, deux canaux chacun',
        );

        foreach ([Reference::CLIENT_MARIE, Reference::CLIENT_JOHN] as $client) {
            self::assertSame(
                EnvoisEnregistres::LES_DEUX_CANAUX,
                $this->messages->canauxUtilises(
                    EnvoisEnregistres::TYPE_ALERTE_METEO,
                    $client['email'],
                ),
                'le message part par SMS et par e-mail',
            );
            self::assertEquals(
                Reference::instant('2026-07-19 18:00'),
                $this->messages->dateDenvoi(
                    EnvoisEnregistres::TYPE_ALERTE_METEO,
                    EnvoisEnregistres::CANAL_EMAIL,
                    $client['email'],
                ),
                'chaque envoi laisse une trace portant sa date',
            );
        }
    }

    /**
     * AC-4 : une sortie maintenue ne donne lieu à aucun second message.
     *
     * Le silence vaut maintien. Ajouter un message rassurant contredirait la
     * règle que le client a posée deux fois.
     */
    public function test_CASE_CANCEL_04_sortie_maintenue_aucun_second_message(): void
    {
        $sortie = $this->sortieDuMatin();
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $this->mettreEnAlerte(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        $this->horloge->nousSommesLe('2026-07-19 18:00');
        $this->envoyerLesMessagesProgrammes();

        // Le gérant ne prend aucune décision.
        $this->horloge->nousSommesLe('2026-07-20 05:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            0,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION),
            'sans annulation, aucun second message ne part',
        );
        self::assertSame(
            2,
            $this->messages->nombreDenvois(),
            'un seul message par canal a été envoyé à ce client, celui de l\'alerte',
        );

        $this->horloge->nousSommesLe('2026-07-20 07:00');
        self::assertSame(
            StatutDeSortie::EN_ALERTE,
            $this->creneau(Reference::CRENEAU_MATIN)->statutDeLaSortie(Reference::TI_KAP),
            'la sortie reste en alerte jusqu\'au départ, puis a lieu',
        );
    }

    /**
     * AC-5 : une sortie annulée donne lieu à un message de confirmation
     * 2 heures avant l'heure de départ prévue.
     */
    public function test_CASE_CANCEL_05_annulation_confirmee_deux_heures_avant_le_depart(): void
    {
        $sortie = $this->sortieDuMatin();
        $marie = $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 2);
        $john = $this->monde->reservationPayee($sortie, Reference::CLIENT_JOHN, adultes: 2, enfants: 2);

        $this->mettreEnAlerte(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);
        $this->horloge->nousSommesLe('2026-07-19 18:00');
        $this->envoyerLesMessagesProgrammes();

        $this->horloge->nousSommesLe('2026-07-20 04:30');
        $this->annulerCreneau(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        self::assertSame(
            StatutDeSortie::ANNULEE,
            $this->creneau(Reference::CRENEAU_MATIN)->statutDeLaSortie(Reference::TI_KAP),
        );

        $this->envoyerLesMessagesProgrammes();
        self::assertSame(
            0,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION),
            'le message ne part pas à la décision, mais deux heures avant le départ',
        );

        $this->horloge->nousSommesLe('2026-07-20 05:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            4,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION),
            'deux clients, deux canaux chacun',
        );
        self::assertEquals(
            Reference::instant('2026-07-20 05:00'),
            $this->messages->dateDenvoi(
                EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
                EnvoisEnregistres::CANAL_SMS,
                Reference::CLIENT_MARIE['email'],
            ),
        );

        self::assertSame(
            Reference::prixDauphins(2),
            $this->paiement->montantRembourse($marie),
            'remboursement intégral, sans retenue',
        );
        self::assertSame(
            Reference::prixDauphins(2, 2),
            $this->paiement->montantRembourse($john),
        );
        self::assertSame(2, $this->paiement->nombreDeRemboursements());
    }

    /**
     * AC-6 : un créneau en alerte reste réservable jusqu'à son heure de
     * fermeture habituelle, le risque étant signalé.
     */
    public function test_CASE_CANCEL_06_creneau_en_alerte_reste_reservable_jusqua_la_fermeture(): void
    {
        $sortie = $this->sortieDeLapresMidi();
        $this->monde->placesVendues($sortie, Reference::TI_KAP_CAPACITE - 4);

        $this->mettreEnAlerte(Reference::JOUR_EN_SAISON, Reference::CRENEAU_APRES_MIDI);

        $this->horloge->nousSommesLe('2026-07-20 11:00');
        $creneau = $this->creneau(Reference::CRENEAU_APRES_MIDI);

        self::assertTrue(
            $creneau->risqueDannulationSignale(),
            'le risque est signalé au client avant qu\'il ne valide',
        );
        self::assertTrue($creneau->estReservable());

        $reservation = $this->monde->reservationImmobilisee(
            $sortie,
            Reference::CLIENT_KARIM,
            adultes: 2,
        );
        self::assertNotSame('', $reservation, 'le client peut réserver 2 places');

        // Midi : la fermeture habituelle du créneau de 14h s'applique,
        // indépendamment de l'alerte, cf. SPEC-BOOKING-04.
        $this->horloge->nousSommesLe('2026-07-20 12:00');
        $creneauFerme = $this->creneau(Reference::CRENEAU_APRES_MIDI);

        self::assertFalse($creneauFerme->estReservable());
        self::assertSame(
            StatutDeSortie::EN_ALERTE,
            $creneauFerme->statutDeLaSortie(Reference::TI_KAP),
            'l\'alerte, elle, court jusqu\'à l\'heure de départ',
        );
    }

    /**
     * AC-7 : un client ayant réservé après l'envoi de l'alerte reçoit la
     * confirmation d'annulation.
     *
     * La liste des destinataires se calcule au moment de l'annulation, pas au
     * moment de l'alerte.
     */
    public function test_CASE_CANCEL_07_client_inscrit_apres_alerte_recoit_la_confirmation(): void
    {
        $sortie = $this->sortieDeLapresMidi();

        $this->mettreEnAlerte(Reference::JOUR_EN_SAISON, Reference::CRENEAU_APRES_MIDI);
        $this->horloge->nousSommesLe('2026-07-19 18:00');
        $this->envoyerLesMessagesProgrammes();

        $this->horloge->nousSommesLe('2026-07-20 11:00');
        $tardif = $this->monde->reservationPayee(
            $sortie,
            Reference::CLIENT_KARIM,
            adultes: 1,
        );

        self::assertSame(
            [],
            $this->messages->envois(
                EnvoisEnregistres::TYPE_ALERTE_METEO,
                destinataire: Reference::CLIENT_KARIM['email'],
            ),
            'ce client n\'a jamais reçu l\'alerte de la veille',
        );

        $this->horloge->nousSommesLe('2026-07-20 11:30');
        $this->annulerCreneau(Reference::JOUR_EN_SAISON, Reference::CRENEAU_APRES_MIDI);

        $this->horloge->nousSommesLe('2026-07-20 12:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            EnvoisEnregistres::LES_DEUX_CANAUX,
            $this->messages->canauxUtilises(
                EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
                Reference::CLIENT_KARIM['email'],
            ),
            'il reçoit tout de même la confirmation d\'annulation',
        );
        self::assertSame(
            Reference::prixDauphins(1),
            $this->paiement->montantRembourse($tardif),
            'et il est remboursé intégralement',
        );
    }

    /**
     * AC-8 : une alerte posée après l'heure d'envoi programmée part
     * immédiatement, au lieu d'attendre l'horaire du lendemain.
     */
    public function test_CASE_CANCEL_08_alerte_posee_apres_lheure_part_immediatement(): void
    {
        $sortie = $this->sortieDuMatin();
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 1);

        $this->horloge->nousSommesLe('2026-07-19 21:00');
        $this->mettreEnAlerte(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN);

        self::assertSame(
            2,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_ALERTE_METEO),
            'un client, deux canaux, sans attendre le passage d\'une tâche',
        );
        self::assertEquals(
            Reference::instant('2026-07-19 21:00'),
            $this->messages->dateDenvoi(
                EnvoisEnregistres::TYPE_ALERTE_METEO,
                EnvoisEnregistres::CANAL_SMS,
                Reference::CLIENT_MARIE['email'],
            ),
            'l\'envoi est daté de 21h00, pas repoussé au lendemain',
        );
    }

    /**
     * AC-9 : l'heure d'envoi de l'alerte et le délai de confirmation sont
     * modifiables depuis l'espace de gestion, et les envois à venir suivent les
     * nouvelles valeurs.
     */
    public function test_CASE_CANCEL_09_horaires_modifies_appliques_aux_envois_a_venir(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            self::LENDEMAIN_DU_JOUR_PIVOT,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
        );
        $this->monde->reservationPayee($sortie, Reference::CLIENT_MARIE, adultes: 1);

        $this->horloge->nousSommesLe('2026-07-20 09:00');
        $this->mettreEnAlerte(self::LENDEMAIN_DU_JOUR_PIVOT, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->monde->parametresDenvoi(
            heureDenvoiDeLalerte: '17:00',
            delaiDeConfirmationEnHeures: 3,
        );

        $this->horloge->nousSommesLe('2026-07-20 17:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertSame(
            2,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_ALERTE_METEO),
            'la nouvelle heure est lue au moment de l\'envoi, pas figée à la mise en alerte',
        );

        $this->envoyerLesMessagesProgrammes();
        self::assertSame(
            2,
            $this->messages->nombreDenvois(EnvoisEnregistres::TYPE_ALERTE_METEO),
            'un envoi déjà parti n\'est pas rejoué',
        );

        $this->horloge->nousSommesLe('2026-07-21 06:00');
        $this->annulerCreneau(self::LENDEMAIN_DU_JOUR_PIVOT, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->horloge->nousSommesLe('2026-07-21 07:00');
        $this->envoyerLesMessagesProgrammes();

        self::assertEquals(
            Reference::instant('2026-07-21 07:00'),
            $this->messages->dateDenvoi(
                EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
                EnvoisEnregistres::CANAL_EMAIL,
                Reference::CLIENT_MARIE['email'],
            ),
            'trois heures avant un départ de 10h00',
        );
    }

    /** La journée du jour pivot, trois créneaux, deux bateaux à 10h. */
    private function journeeDeReference(): void
    {
        $this->monde->sortieProgrammee(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MATIN, Reference::TI_KAP);
        $this->monde->sortieProgrammee(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE, Reference::TI_KAP);
        $this->monde->sortieProgrammee(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE, Reference::LE_GRAND_BLEU);
        $this->monde->sortieProgrammee(Reference::JOUR_EN_SAISON, Reference::CRENEAU_APRES_MIDI, Reference::TI_KAP);
    }

    private function sortieDuMatin(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MATIN,
            Reference::TI_KAP,
        );
    }

    private function sortieDeLapresMidi(): string
    {
        return $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
        );
    }

    private function mettreEnAlerte(string $jour, string $heure): void
    {
        (new MettreEnAlerte($this->horloge, $this->messages))->executer($jour, $heure);
    }

    private function annulerCreneau(string $jour, string $heure): void
    {
        (new AnnulerCreneau($this->horloge, $this->messages, $this->paiement))
            ->executer($jour, $heure);
    }

    private function envoyerLesMessagesProgrammes(): void
    {
        (new EnvoyerLesMessagesProgrammes($this->horloge, $this->messages))->executer();
    }

    private function creneau(string $heure): VueDeCreneau
    {
        return (new ConsulterUnCreneau($this->horloge))
            ->executer(Reference::JOUR_EN_SAISON, $heure);
    }
}
