<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\CreationDunBateau;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-ADMIN-05 - ce qui fait un bateau valide, règle pure.
 *
 * Le nom identifie le bateau sur le planning et pour le gérant : il doit rester
 * unique. Et une capacité n'est ni nulle ni fractionnaire, on n'embarque pas
 * une demi-personne.
 */
final class CreationDunBateauTest extends TestCase
{
    /**
     * AC-3 et AC-4 : un nom déjà pris ou une capacité invalide sont refusés.
     */
    public function test_CASE_ADMIN_12_nom_deja_pris_ou_capacite_invalide_refuses(): void
    {
        $regle = new CreationDunBateau();
        $flotte = [Reference::TI_KAP, Reference::LE_GRAND_BLEU];

        self::assertFalse(
            $regle->estValide(Reference::TI_KAP, $flotte, capacite: 10),
            'le nom identifie le bateau, il doit rester unique',
        );
        self::assertFalse($regle->estValide('Le Petit Bleu', $flotte, capacite: 0));
        self::assertFalse(
            $regle->estValide('Le Petit Bleu', $flotte, capacite: 8.5),
            'on n\'embarque pas une demi-personne',
        );
        self::assertTrue($regle->estValide('Le Petit Bleu', $flotte, capacite: 8));
    }
}
