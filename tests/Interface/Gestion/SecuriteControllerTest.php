<?php

declare(strict_types=1);

namespace App\Tests\Interface\Gestion;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class SecuriteControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-01 09:00';
    }

    public function test_un_acces_non_authentifie_est_renvoye_vers_la_connexion(): void
    {
        $this->client->request('GET', '/fr/gestion');

        self::assertResponseRedirects('/fr/gestion/connexion');
    }

    public function test_des_identifiants_valides_ouvrent_la_journee(): void
    {
        $this->client->request('GET', '/fr/gestion/connexion');
        $formulaire = $this->client->getCrawler()->filter('form')->form([
            '_username' => Reference::EMAIL_DU_GERANT,
            '_password' => Reference::MOT_DE_PASSE_DU_GERANT,
        ]);
        $this->client->submit($formulaire);

        self::assertResponseRedirects('/fr/gestion');
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'La journée');
    }

    public function test_un_mot_de_passe_incorrect_reste_sur_la_connexion(): void
    {
        $this->client->request('GET', '/fr/gestion/connexion');
        $formulaire = $this->client->getCrawler()->filter('form')->form([
            '_username' => Reference::EMAIL_DU_GERANT,
            '_password' => 'ce-nest-pas-le-mot-de-passe',
        ]);
        $this->client->submit($formulaire);

        self::assertResponseRedirects('/fr/gestion/connexion');
        $this->client->followRedirect();

        self::assertSelectorTextContains('.reservation__erreur', 'incorrect');
    }
}
