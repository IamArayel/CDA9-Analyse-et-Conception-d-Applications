<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * Ce qu'un bon cadeau ou un code d'avoir donne à voir.
 *
 * Un code ne porte ni type de sortie ni catégorie de passager : il vaut un
 * montant, et s'applique à n'importe quelle réservation. C'est la règle
 * inversée en v4 du cahier des charges.
 */
final class VueDeCode
{
    /** @param int $montant en centimes */
    public function __construct(
        private readonly int $montant,
        private readonly DateTimeImmutable $expireLe,
        private readonly bool $estUtilisable,
    ) {
    }

    /** En centimes. */
    public function montant(): int
    {
        return $this->montant;
    }

    /** Fin du jour anniversaire, bornes incluses : hypothèse d'équipe. */
    public function expireLe(): DateTimeImmutable
    {
        return $this->expireLe;
    }

    public function estUtilisable(): bool
    {
        return $this->estUtilisable;
    }
}
