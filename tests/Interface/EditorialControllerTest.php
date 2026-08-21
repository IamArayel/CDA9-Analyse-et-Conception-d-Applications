<?php

declare(strict_types=1);

namespace App\Tests\Interface;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class EditorialControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-01 09:00';
    }

    public function test_la_frise_de_saison_place_juin_en_partiel_et_juillet_en_baleines(): void
    {
        $this->client->request('GET', '/fr/sorties');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.frise__mois--partiel');
        self::assertSelectorExists('.frise__mois--baleines');
        self::assertSelectorExists('.frise__mois--dauphins');
    }

    public function test_un_bateau_privatisable_affiche_son_forfait(): void
    {
        $this->client->request('GET', '/fr/bateaux');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.editorial__carte', Reference::TI_KAP);
        self::assertSelectorTextContains(
            '.editorial__carte',
            number_format(Reference::TI_KAP_FORFAIT_PRIVATISATION / 100, 2, ',', ''),
        );
    }

    public function test_la_grille_tarifaire_affiche_les_prix_de_reference(): void
    {
        $this->client->request('GET', '/fr/tarifs');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.tarifs__table', 'Enfants de moins de 4 ans');
    }
}
