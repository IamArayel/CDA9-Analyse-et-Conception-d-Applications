<?php

declare(strict_types=1);

namespace App\Tests\Interface\Gestion;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class CreneauControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-19 10:00';
    }

    public function test_le_detail_affiche_les_inscrits_et_leur_solde(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $this->connecterLeGerant();
        $this->client->request('GET', sprintf('/fr/gestion/creneau/2026-07-20/%s', Reference::CRENEAU_APRES_MIDI));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.gestion__export-table', 'Dupont');
        self::assertSelectorTextContains('.gestion__export-table', 'Confirmée');
    }

    public function test_une_reservation_immobilisee_napparait_pas_comme_inscrite(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $this->monde->reservationImmobilisee($sortie, Reference::CLIENT_JOHN, adultes: 1);

        $this->connecterLeGerant();
        $this->client->request('GET', sprintf('/fr/gestion/creneau/2026-07-20/%s', Reference::CRENEAU_APRES_MIDI));

        self::assertSelectorTextNotContains('.gestion__export-table', 'Smith');
        self::assertSelectorExists('.gestion__ligne-immobilisee');
    }
}
