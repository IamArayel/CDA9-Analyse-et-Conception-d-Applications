<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\ValiditeDunAvoir;
use App\Domaine\StatutDeCode;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-10 - validité d'un code d'avoir, règle pure.
 *
 * La validité d'un an vient de CR-04/Q04 : elle infirme l'hypothèse d'équipe
 * initiale, qui ne prévoyait aucune expiration. Un avoir se comporte donc comme
 * un bon cadeau sur ce point.
 */
final class ValiditeDunAvoirTest extends TestCase
{
    /**
     * AC-3 et AC-6 : un code d'avoir déjà utilisé, ou émis il y a plus d'un an,
     * est refusé.
     */
    public function test_CASE_BOOKING_34_avoir_utilise_ou_expire_refuse(): void
    {
        $validite = new ValiditeDunAvoir();
        $emission = Reference::instant('2026-07-20 10:00');

        self::assertFalse(
            $validite->estUtilisable(
                StatutDeCode::UTILISE,
                $emission,
                Reference::instant('2026-08-20 10:00'),
            ),
            'un code déjà consommé ne se réutilise pas, même dans sa période de validité',
        );

        self::assertFalse(
            $validite->estUtilisable(
                StatutDeCode::DISPONIBLE,
                $emission,
                Reference::instant('2027-07-21 10:00'),
            ),
            'la validité d\'un an est dépassée',
        );

        self::assertTrue(
            $validite->estUtilisable(
                StatutDeCode::DISPONIBLE,
                $emission,
                Reference::instant('2027-07-20 10:00'),
            ),
            'le jour anniversaire compte encore, comme pour un bon cadeau',
        );
    }
}
