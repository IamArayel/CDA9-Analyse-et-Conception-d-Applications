<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\ParcoursDeReservation;
use App\Tests\CasDapplication;

/**
 * SPEC-BOOKING-11 - langue du parcours client.
 *
 * Aucune détection automatique : le français s'applique tant que le client n'a
 * rien choisi, quelle que soit la configuration de son navigateur. Et les
 * données saisies survivent au changement de langue, sans quoi le client
 * recommence son formulaire.
 */
final class LangueDuParcoursTest extends CasDapplication
{
    protected function instantInitial(): string
    {
        return '2026-07-18 14:00';
    }

    /**
     * AC-3 et AC-4 : le français s'applique par défaut, et une bascule de
     * langue ne perd rien de ce qui a été saisi.
     */
    public function test_CASE_BOOKING_36_francais_par_defaut_et_bascule_sans_perte(): void
    {
        $parcours = new ParcoursDeReservation();
        $parcours->demarrer(langueDuNavigateur: 'en');

        self::assertSame(
            'fr',
            $parcours->langue(),
            'le navigateur en anglais ne déclenche aucune détection automatique',
        );

        $saisie = [
            'nom' => 'Smith',
            'prenom' => 'John',
            'email' => 'john.smith@example.test',
        ];
        $parcours->renseigner($saisie);
        $parcours->basculerEn('en');

        self::assertSame('en', $parcours->langue());
        self::assertSame(
            $saisie,
            $parcours->champsSaisis(),
            'les trois champs saisis sont conservés',
        );
    }
}
