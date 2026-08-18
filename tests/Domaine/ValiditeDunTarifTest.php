<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\ValiditeDunTarif;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-ADMIN-02 - ce qui peut entrer dans la grille tarifaire, règle pure.
 *
 * Le refus du 0 € est une décision d'équipe : le client n'a jamais prévu de
 * sortie gratuite. Il est écrit ici pour être discutable, plutôt que caché dans
 * une validation de formulaire.
 */
final class ValiditeDunTarifTest extends TestCase
{
    /**
     * AC-3 : un tarif négatif ou nul est refusé.
     */
    public function test_CASE_ADMIN_05_tarif_negatif_ou_nul_refuse(): void
    {
        $regle = new ValiditeDunTarif();

        self::assertFalse($regle->estValide(Reference::euros(-10)));
        self::assertFalse(
            $regle->estValide(0),
            'aucune sortie gratuite n\'a jamais été prévue par le client',
        );
        self::assertTrue($regle->estValide(Reference::DAUPHINS_PRIX_ADULTE));
    }
}
