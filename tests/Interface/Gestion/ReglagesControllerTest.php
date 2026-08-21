<?php

declare(strict_types=1);

namespace App\Tests\Interface\Gestion;

use App\Application\ConsulterLaGrilleTarifaire;
use App\Tests\CasDinterface;

final class ReglagesControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-19 10:00';
    }

    public function test_modifier_la_grille_ne_rattrape_pas_les_reservations_existantes(): void
    {
        $this->connecterLeGerant();

        $this->client->request('POST', '/fr/gestion/reglages/tarifs', [
            'adulte_BALEINES' => '99',
            'enfant_BALEINES' => '50',
            'adulte_DAUPHINS' => '55',
            'enfant_DAUPHINS' => '35',
        ]);

        self::assertResponseRedirects('/fr/gestion/reglages');

        $grille = static::getContainer()->get(ConsulterLaGrilleTarifaire::class)->executer();
        self::assertSame(9900, $grille['BALEINES']['adulte']);
        self::assertSame(5500, $grille['DAUPHINS']['adulte']);
    }

    public function test_fermer_un_jour_deja_reserve_est_accepte_et_le_signale(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-25',
            '10:00',
            'Ti Kap',
            'DAUPHINS',
        );
        $this->monde->reservationConfirmee($sortie, [
            'nom' => 'Dupont', 'prenom' => 'Marie', 'email' => 'marie.dupont@example.test',
            'telephone_mobile' => '0692000001', 'langue' => 'fr',
        ], adultes: 1);

        $this->connecterLeGerant();
        $this->client->request('POST', '/fr/gestion/reglages/fermeture/ajouter', ['jour' => '2026-07-25']);
        $this->client->followRedirect();

        self::assertSelectorTextContains('.message-flash', 'accepté');
    }
}
