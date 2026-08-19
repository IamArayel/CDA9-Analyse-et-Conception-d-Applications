<?php

declare(strict_types=1);

namespace App\Domaine\Service;

use App\Domaine\TypeDeSortie;

/**
 * Le montant d'une réservation (SPEC-BOOKING-06).
 *
 * Tarif adulte fois le nombre d'adultes, plus tarif enfant fois le nombre
 * d'enfants, selon le type de sortie. La répartition entre adultes et enfants
 * est déclarative : aucun âge n'est collecté, donc rien ne la vérifie.
 *
 * **La grille est un paramètre, pas une constante du domaine.** Elle est
 * modifiable par le gérant (SPEC-ADMIN-02) : la couche application lui passe
 * celle de la table `tarif`. La valeur par défaut est la grille initiale, celle
 * qui amorce la table.
 *
 * Tous les montants sont en centimes, pour que rien ne dépende d'un arrondi
 * flottant.
 */
final class CalculDuMontant
{
    public const GRILLE_INITIALE = [
        TypeDeSortie::BALEINES => ['adulte' => 6500, 'enfant' => 4000],
        TypeDeSortie::DAUPHINS => ['adulte' => 5000, 'enfant' => 3000],
    ];

    /** @param array<string, array{adulte: int, enfant: int}> $grille */
    public function __construct(private readonly array $grille = self::GRILLE_INITIALE)
    {
    }

    /** @return int en centimes */
    public function pour(string $typeDeSortie, int $adultes, int $enfants): int
    {
        $tarif = $this->grille[$typeDeSortie];

        return $adultes * $tarif['adulte'] + $enfants * $tarif['enfant'];
    }
}
