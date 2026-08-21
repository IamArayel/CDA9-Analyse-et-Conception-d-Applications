<?php

declare(strict_types=1);

namespace App\Tests\Interface;

use App\Tests\CasDinterface;
use App\Tests\JeuDeDonneesDeReference as Reference;

final class CalendrierControllerTest extends CasDinterface
{
    protected function instantInitial(): string
    {
        return '2026-07-20 09:00';
    }

    public function test_un_depart_programme_affiche_son_bateau_et_ses_places_restantes(): void
    {
        $this->monde->sortieProgrammee(
            '2026-07-20',
            Reference::CRENEAU_APRES_MIDI,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );

        $this->client->request('GET', '/fr/reserver');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.calendrier__carte-bateau', Reference::TI_KAP);
        self::assertSelectorTextContains(
            '.calendrier__carte--disponible .calendrier__carte-etat',
            sprintf('%d places', Reference::TI_KAP_CAPACITE),
        );
    }

    public function test_un_jour_de_fermeture_ne_montre_aucun_depart_meme_deja_programme(): void
    {
        $this->horloge->nousSommesLe('2026-12-25 09:00');

        $this->monde->sortieProgrammee(
            '2026-12-25',
            Reference::CRENEAU_MATIN,
            Reference::TI_KAP,
            Reference::SORTIE_DAUPHINS,
        );

        $this->client->request('GET', '/fr/reserver');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextNotContains('.calendrier__grille', Reference::TI_KAP);
        self::assertSelectorTextContains('.calendrier__grille', 'Aucun départ ce jour-là.');
    }
}
