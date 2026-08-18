<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\ValiditeDunCode;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-09 - la validité d'un code dans le temps, règle pure.
 *
 * « Un an » sans autre précision : l'équipe a retenu que la validité court
 * jusqu'à la fin du jour anniversaire, bornes incluses. C'est une hypothèse,
 * et ce test est l'endroit où elle est écrite noir sur blanc.
 *
 * Sans instant passé en paramètre, vérifier cette règle imposerait d'attendre
 * un an (ADR-005, option A).
 */
final class ValiditeDunCodeTest extends TestCase
{
    /**
     * AC-6 : un code est accepté le jour anniversaire et refusé le lendemain.
     */
    public function test_CASE_BOOKING_18_bon_cadeau_expire_le_lendemain_de_lanniversaire(): void
    {
        $achat = Reference::instant('2026-07-20 10:00');
        $validite = new ValiditeDunCode();

        self::assertTrue(
            $validite->estValide($achat, Reference::instant('2027-07-20 23:00')),
            'le jour anniversaire compte encore',
        );
        self::assertFalse(
            $validite->estValide($achat, Reference::instant('2027-07-21 00:01')),
            'le lendemain, le code est expiré',
        );
    }
}
