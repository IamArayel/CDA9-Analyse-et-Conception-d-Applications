<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Un mois de la frise de saison de l'écran « Les sorties » (README §2).
 *
 * `type` vaut `TypeDeSortie::BALEINES`, `TypeDeSortie::DAUPHINS`, ou
 * `'PARTIEL'` pour un mois que la saison traverse sans le couvrir en entier
 * (juin, qui ouvre le 15).
 */
final class VueDunMoisDeSaison
{
    public const PARTIEL = 'PARTIEL';

    public function __construct(
        private readonly int $mois,
        private readonly string $type,
    ) {
    }

    /** 1 à 12. */
    public function mois(): int
    {
        return $this->mois;
    }

    public function type(): string
    {
        return $this->type;
    }
}
