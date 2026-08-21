<?php

declare(strict_types=1);

namespace App\Tests\Interface;

use App\Tests\CasDinterface;

final class BonCadeauControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-01 09:00';
    }

    public function test_un_achat_valide_delivre_un_code_avec_sa_date_dexpiration(): void
    {
        $this->client->request('POST', '/fr/bon-cadeau', [
            'montant' => '150',
            'beneficiaire' => 'Karim Benali',
            'courriel_acheteur' => 'marie.dupont@example.test',
            'message' => 'Joyeux anniversaire !',
        ]);

        self::assertResponseRedirects();
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.bon-cadeau__code', '');
        self::assertSelectorExists('.bon-cadeau__code .chiffres');
        self::assertSelectorTextContains('.bon-cadeau__panneau', '2027');
    }

    public function test_un_montant_nul_reaffiche_le_formulaire_sans_creer_de_bon(): void
    {
        $this->client->request('POST', '/fr/bon-cadeau', [
            'montant' => '0',
            'beneficiaire' => 'Karim Benali',
            'courriel_acheteur' => 'marie.dupont@example.test',
            'message' => '',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.reservation__erreur', 'montant');
        self::assertSelectorNotExists('.bon-cadeau__code');
    }
}
