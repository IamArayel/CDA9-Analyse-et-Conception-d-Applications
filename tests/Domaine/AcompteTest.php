<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Politique\Acompte;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-07 - le calcul de l'acompte, règle pure.
 *
 * 30 % pour une sortie, 50 % pour une privatisation, **arrondi au centime**.
 * Arrondir à l'euro ferait perdre ou gagner jusqu'à 50 centimes par
 * réservation, ce que ni le gérant ni le passager n'accepteraient sur un
 * relevé.
 */
final class AcompteTest extends TestCase
{
    /**
     * AC-9 : le montant de l'acompte est arrondi au centime.
     */
    public function test_CASE_BOOKING_38_acompte_arrondi_au_centime(): void
    {
        $acompte = new Acompte();

        self::assertSame(
            Reference::euros(19.50),
            $acompte->pourUneSortie(Reference::prixBaleines(1)),
            '30 % de 65 € valent 19,50 €, ni 19 € ni 20 €',
        );
        self::assertSame(
            Reference::euros(300),
            $acompte->pourUnePrivatisation(Reference::TI_KAP_FORFAIT_PRIVATISATION),
            'la moitié du forfait du Ti Kap',
        );
    }
}
