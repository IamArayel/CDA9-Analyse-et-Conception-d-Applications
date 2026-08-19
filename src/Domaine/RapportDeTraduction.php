<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Le résultat de la comparaison des catalogues de traduction (SPEC-NFR-02 AC-1).
 *
 * Ce rapport sert un test qui est un **garde-fou dans la durée** plus qu'un
 * contrôle ponctuel : un contenu ajouté après la livraison sans sa traduction
 * le fait échouer. Il ne juge pas la qualité de la traduction, qui relève d'une
 * relecture humaine.
 */
final class RapportDeTraduction
{
    /**
     * @param list<string> $clesManquantes            clés présentes dans une langue et pas dans l'autre
     * @param list<string> $valeursVides              clés traduites par une chaîne vide
     * @param list<string> $gabaritsDeMessageManquants gabarits des messages automatiques absents d'une langue
     */
    public function __construct(
        private readonly array $clesManquantes,
        private readonly array $valeursVides,
        private readonly array $gabaritsDeMessageManquants,
    ) {
    }

    /** @return list<string> */
    public function clesManquantes(): array
    {
        return $this->clesManquantes;
    }

    /** @return list<string> */
    public function valeursVides(): array
    {
        return $this->valeursVides;
    }

    /** @return list<string> */
    public function gabaritsDeMessageManquants(): array
    {
        return $this->gabaritsDeMessageManquants;
    }
}
