<?php

declare(strict_types=1);

namespace App\Application;

use App\Domaine\Service\CalculDuMontant;
use App\Domaine\TypeDeSortie;
use App\Infrastructure\Persistance\TarifRepository;

/**
 * Les montants suggérés à l'achat d'un bon cadeau.
 *
 * Un bon ne porte aucun type de sortie (v4 du cahier des charges) : les deux
 * suggestions ne sont qu'un repère de prix, celui d'une place baleines, la
 * sortie la plus chère de la grille — le bon reste utilisable sur n'importe
 * quelle sortie.
 */
final class ConsulterLesSuggestionsDeBonCadeau
{
    public function __construct(private readonly TarifRepository $tarifs)
    {
    }

    /** @return array{unePlace: int, deuxPlaces: int} en centimes */
    public function executer(): array
    {
        $montant = new CalculDuMontant($this->tarifs->grille());
        $unePlace = $montant->pour(TypeDeSortie::BALEINES, 1, 0);

        return [
            'unePlace' => $unePlace,
            'deuxPlaces' => $unePlace * 2,
        ];
    }
}
