<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\ComplexiteDuMotDePasse;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-ADMIN-01 - complexité du mot de passe du gérant, règle pure.
 *
 * Huit caractères au moins, une majuscule, une minuscule, un chiffre, un
 * caractère spécial. Chaque condition manquante suffit à refuser.
 */
final class ComplexiteDuMotDePasseTest extends TestCase
{
    /**
     * AC-2 : un mot de passe non conforme est refusé à la définition.
     */
    public function test_CASE_ADMIN_03_mot_de_passe_non_conforme_refuse(): void
    {
        $regle = new ComplexiteDuMotDePasse();

        self::assertFalse(
            $regle->estConforme('Abc1!de'),
            'sept caractères ne suffisent pas, même bien composés',
        );
        self::assertFalse(
            $regle->estConforme('Abcdefgh1234'),
            'douze caractères sans caractère spécial ne suffisent pas',
        );
        self::assertTrue(
            $regle->estConforme('Abc1!def'),
            'huit caractères exactement suffisent : la borne est inclusive',
        );
    }
}
