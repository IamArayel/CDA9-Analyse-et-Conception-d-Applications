<?php

declare(strict_types=1);

namespace App\Tests\Interface;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class PaiementControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-20 09:00';
    }

    public function test_un_paiement_accepte_confirme_la_reservation(): void
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
            'telephone_mobile' => '0692000001',
        ]);
        $this->client->followRedirect();

        $formulaire = $this->client->getCrawler()->filter('.reservation__recapitulatif form')->form();
        $this->client->submit($formulaire);

        self::assertResponseRedirects();
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', "C'est réservé");
        self::assertSame(1, $this->paiement->nombreDencaissements());
    }

    public function test_un_paiement_refuse_laisse_la_reservation_tenue_et_reessayable(): void
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
            'telephone_mobile' => '0692000001',
        ]);
        $this->client->followRedirect();

        $this->paiement->refuseraLaProchaineTransaction();

        $formulaire = $this->client->getCrawler()->filter('.reservation__recapitulatif form')->form();
        $this->client->submit($formulaire);

        self::assertResponseRedirects(sprintf('/fr/reserver/%s', $sortie));
        $this->client->followRedirect();

        self::assertSelectorExists('[data-expire-a]');
        self::assertSelectorTextContains('.message-flash', 'refusé');
        self::assertTrue($this->paiement->aucunRemboursementDemande());
    }
}
