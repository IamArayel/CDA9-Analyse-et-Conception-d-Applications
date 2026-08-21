<?php

declare(strict_types=1);

namespace App\Tests\Interface;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class ReservationControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-20 09:00';
    }

    public function test_une_reservation_valide_est_immobilisee_et_affiche_lacompte(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );

        $this->client->request('POST', sprintf('/fr/reserver/%s', $sortie), [
            'adultes' => '2',
            'enfants' => '1',
            'nom' => 'Dupont',
            'prenom' => 'Marie',
            'email' => 'marie.dupont@example.test',
            'telephone_mobile' => '0692000001',
        ]);

        self::assertResponseRedirects(sprintf('/fr/reserver/%s', $sortie));
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        $attendu = Reference::acompteSortie(Reference::prixDauphins(2, 1)) / 100;
        self::assertSelectorTextContains('.reservation__acompte', number_format($attendu, 2, ',', ''));
        self::assertSelectorExists('[data-expire-a]');
    }

    public function test_un_mobile_invalide_reaffiche_le_formulaire_sans_creer_de_reservation(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );

        $this->client->request('POST', sprintf('/fr/reserver/%s', $sortie), [
            'adultes' => '1',
            'enfants' => '0',
            'nom' => 'Dupont',
            'prenom' => 'Marie',
            'email' => 'marie.dupont@example.test',
            'telephone_mobile' => '0262123456',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.champ--erreur');
        self::assertSelectorTextContains('.reservation__erreur-champ', 'valide');
        self::assertSelectorNotExists('[data-expire-a]');
    }
}
