<?php

declare(strict_types=1);

namespace App\Tests\Interface;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AccueilControllerTest extends WebTestCase
{
    public function test_laccueil_affiche_le_titre_et_la_navigation_en_francais(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'On regarde par en dessous.');
        self::assertSelectorTextContains('.entete__nav', 'Réserver');
    }

    public function test_une_locale_hors_frEn_est_refusee(): void
    {
        $client = static::createClient();
        $client->request('GET', '/xx/');

        self::assertResponseStatusCodeSame(404);
    }
}
