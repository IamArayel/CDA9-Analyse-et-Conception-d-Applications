<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\AnnulerCreneau;
use App\Application\MettreEnAlerte;
use App\Application\Tache\EnvoyerLesMessagesProgrammes;
use App\Tests\CasDapplication;
use App\Tests\Doublures\EnvoisEnregistres;
use App\Tests\JeuDeDonneesDeReference as Reference;

/**
 * SPEC-NFR-02 - langue des messages automatiques.
 *
 * La langue lue est celle enregistrée sur la réservation, jamais celle du
 * dernier écran consulté ni celle du navigateur : un message part parfois
 * plusieurs heures plus tard, sans personne devant l'écran.
 */
final class LangueDesMessagesTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-17 09:00';
    }

    /**
     * AC-2 et AC-3 : les trois messages automatiques partent dans la langue
     * choisie à la réservation, le français par défaut.
     */
    public function test_CASE_NFR_01_messages_dans_la_langue_de_la_reservation(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            Reference::JOUR_EN_SAISON,
            Reference::CRENEAU_MILIEU_DE_MATINEE,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        // CLIENT_JOHN a choisi l'anglais ; ce client-ci n'a rien choisi.
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_JOHN, adultes: 2);
        $this->monde->reservationConfirmee($sortie, [
            'nom' => 'Sans', 'prenom' => 'Choix', 'email' => 'sans.choix@example.test',
            'telephone_mobile' => '0692000005', 'langue' => null,
        ], adultes: 1);

        $this->horloge->nousSommesLe('2026-07-19 09:00');
        ($this->service(MettreEnAlerte::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->horloge->nousSommesLe('2026-07-19 10:00');
        $this->envoyerLesMessagesProgrammes();

        $this->horloge->nousSommesLe('2026-07-19 18:00');
        $this->envoyerLesMessagesProgrammes();

        $this->horloge->nousSommesLe('2026-07-19 19:00');
        ($this->service(AnnulerCreneau::class))
            ->executer(Reference::JOUR_EN_SAISON, Reference::CRENEAU_MILIEU_DE_MATINEE);

        $this->horloge->nousSommesLe('2026-07-20 08:00');
        $this->envoyerLesMessagesProgrammes();

        $lesTroisMessages = [
            EnvoisEnregistres::TYPE_RAPPEL,
            EnvoisEnregistres::TYPE_ALERTE_METEO,
            EnvoisEnregistres::TYPE_CONFIRMATION_ANNULATION,
        ];

        foreach ($lesTroisMessages as $type) {
            self::assertSame(
                'en',
                $this->langueDe($type, Reference::CLIENT_JOHN['email']),
                'la langue vient de la réservation, pas du dernier écran consulté',
            );
            self::assertSame(
                'fr',
                $this->langueDe($type, 'sans.choix@example.test'),
                'le français est la valeur par défaut quand aucun choix n\'a été fait',
            );
        }
    }

    private function langueDe(string $type, string $destinataire): ?string
    {
        return $this->messages->donneesDenvoi(
            $type,
            EnvoisEnregistres::CANAL_EMAIL,
            $destinataire,
        )['langue'] ?? null;
    }

    private function envoyerLesMessagesProgrammes(): void
    {
        ($this->service(EnvoyerLesMessagesProgrammes::class))->executer();
    }
}
