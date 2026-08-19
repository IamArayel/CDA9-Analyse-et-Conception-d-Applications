<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use DateTimeImmutable;

/**
 * Combien de temps vaut un bon cadeau ou un code d'avoir (SPEC-BOOKING-09 AC-6).
 *
 * Le client a dit « un an » sans préciser si le jour anniversaire compte.
 * **Hypothèse d'équipe :** la validité court jusqu'à la fin de ce jour, bornes
 * incluses. Elle est écrite ici, et non enfouie dans une comparaison de dates.
 *
 * L'instant est un paramètre : sans cela, vérifier cette règle imposerait
 * d'attendre un an (ADR-005, option A).
 */
final class ValiditeDunCode
{
    public const DUREE = '+1 year';

    public function estValide(DateTimeImmutable $dateDeDepart, DateTimeImmutable $maintenant): bool
    {
        return $maintenant <= $this->expirationDe($dateDeDepart);
    }

    /** Le dernier instant où le code vaut encore quelque chose. */
    public function expirationDe(DateTimeImmutable $dateDeDepart): DateTimeImmutable
    {
        return $dateDeDepart->modify(self::DUREE)->setTime(23, 59, 59);
    }
}
