<?php

declare(strict_types=1);

namespace App\Tests\Interface\Gestion;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class AnnulationControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-19 10:00';
    }

    public function test_annuler_le_creneau_rembourse_et_libere_les_places(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2);

        $this->connecterLeGerant();
        $this->client->request('POST', sprintf('/fr/gestion/creneau/2026-07-20/%s/annuler', Reference::CRENEAU_APRES_MIDI));

        self::assertResponseRedirects('/fr/gestion');
        self::assertSame(1, $this->paiement->nombreDeRemboursements());
    }

    public function test_un_creneau_deja_parti_ne_peut_plus_etre_annule(): void
    {
        $this->monde->sortieProgrammee(
            '2026-07-18',
            Reference::CRENEAU_MATIN,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );

        $this->connecterLeGerant();
        $this->client->request('POST', sprintf('/fr/gestion/creneau/2026-07-18/%s/annuler', Reference::CRENEAU_MATIN));

        self::assertResponseRedirects(sprintf('/fr/gestion/creneau/2026-07-18/%s', Reference::CRENEAU_MATIN));
        $this->client->followRedirect();
        self::assertSelectorTextContains('.message-flash', 'parti');
        self::assertSame(0, $this->paiement->nombreDeRemboursements());
    }
}
