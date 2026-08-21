<?php

declare(strict_types=1);

namespace App\Tests\Interface\Gestion;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class JourneeControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-19 10:00';
    }

    public function test_les_indicateurs_reflètent_les_reservations_du_jour(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $this->connecterLeGerant();
        $this->client->request('GET', '/fr/gestion?jour=2026-07-20');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.gestion__indicateurs', '2');
        self::assertSelectorTextContains('.gestion__carte', Reference::TI_KAP);
    }

    public function test_un_creneau_sous_le_seuil_a_24h_du_depart_affiche_le_bandeau(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $this->horloge->nousSommesLe('2026-07-19 15:00');
        $this->connecterLeGerant();
        $this->client->request('GET', '/fr/gestion?jour=2026-07-20');

        self::assertSelectorExists('.gestion__bandeau');
        self::assertSelectorTextContains('.gestion__bandeau', Reference::TI_KAP);
    }
}
