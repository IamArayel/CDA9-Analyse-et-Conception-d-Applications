<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * L'accès au temps, vu par le domaine.
 *
 * Le domaine ne lit jamais l'heure système : il la reçoit (ADR-005). En
 * production, l'implémentation rend l'heure réelle du fuseau d'exploitation ;
 * en test, l'instant que le cas a fixé.
 *
 * Cette interface est définie par le domaine et implémentée par
 * l'infrastructure : la dépendance va donc de l'infrastructure vers le domaine,
 * jamais l'inverse.
 */
interface Horloge
{
    public function maintenant(): DateTimeImmutable;
}
