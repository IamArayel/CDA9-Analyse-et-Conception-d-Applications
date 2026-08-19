<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Les deux types de sortie vendus.
 *
 * Des constantes de chaîne plutôt qu'une énumération : la valeur circule telle
 * quelle jusqu'à la colonne `sortie.type_sortie` du MLD, et les cas de test la
 * comparent comme une chaîne.
 */
final class TypeDeSortie
{
    public const BALEINES = 'BALEINES';
    public const DAUPHINS = 'DAUPHINS';

    /** Dans l'ordre où ils sont proposés au client. */
    public const TOUS = [self::BALEINES, self::DAUPHINS];
}
