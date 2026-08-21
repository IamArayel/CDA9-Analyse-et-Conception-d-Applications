<?php

declare(strict_types=1);

namespace App\Tests\Interface;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class ConfirmationControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-20 09:00';
    }

    public function test_une_reservation_confirmee_affiche_ses_chiffres_et_son_agenda(): void
    {
        $sortie = $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );
        $reservation = $this->monde->reservationConfirmee($sortie, Reference::CLIENT_MARIE, adultes: 2, enfants: 1);

        $this->client->request('GET', sprintf('/fr/reservation/%s', $reservation));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.confirmation__chiffres', $reservation);

        $lienAgenda = $this->client->getCrawler()->filter('.confirmation__panneau a')->attr('href');
        $this->client->request('GET', $lienAgenda);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'text/calendar; charset=utf-8');
        self::assertStringContainsString('BEGIN:VEVENT', (string) $this->client->getResponse()->getContent());
    }

    public function test_une_reference_inexistante_est_un_404(): void
    {
        $this->client->request('GET', '/fr/reservation/999999');

        self::assertResponseStatusCodeSame(404);
    }
}
