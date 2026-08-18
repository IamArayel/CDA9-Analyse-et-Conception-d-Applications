<?php

declare(strict_types=1);

namespace App\Tests\Domaine;

use App\Domaine\Service\CalculDuMontant;
use App\Tests\JeuDeDonneesDeReference as Reference;
use PHPUnit\Framework\TestCase;

/**
 * SPEC-BOOKING-06 - calcul du montant d'une réservation, règle pure.
 *
 * La répartition entre adultes et enfants est déclarative : aucun âge n'est
 * collecté, donc rien ne la vérifie. Le calcul, lui, ne dépend que de la grille
 * du type de sortie.
 */
final class CalculDuMontantTest extends TestCase
{
    /**
     * AC-1 et AC-2 : le montant suit la grille du type de sortie, pour une
     * composition identique.
     */
    public function test_CASE_BOOKING_31_montant_selon_la_grille_du_type_de_sortie(): void
    {
        $calcul = new CalculDuMontant();

        self::assertSame(
            Reference::euros(170),
            $calcul->pour(Reference::SORTIE_BALEINES, adultes: 2, enfants: 1),
            'deux adultes à 65 € et un enfant à 40 €',
        );
        self::assertSame(
            Reference::euros(130),
            $calcul->pour(Reference::SORTIE_DAUPHINS, adultes: 2, enfants: 1),
            'la même composition, à la grille dauphins',
        );
    }
}
