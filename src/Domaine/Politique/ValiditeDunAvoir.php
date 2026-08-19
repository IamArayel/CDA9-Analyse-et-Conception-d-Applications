<?php

declare(strict_types=1);

namespace App\Domaine\Politique;

use App\Domaine\StatutDeCode;
use DateTimeImmutable;

/**
 * Quand un code d'avoir est encore utilisable (SPEC-BOOKING-10 AC-3 et AC-6).
 *
 * Deux conditions, et il faut les deux : le code n'a pas déjà servi, et il n'a
 * pas plus d'un an. La validité d'un an vient de CR-04/Q04 ; elle infirme
 * l'hypothèse d'équipe initiale, qui ne prévoyait aucune expiration.
 *
 * La durée est celle du bon cadeau : les deux dispositifs ne diffèrent que par
 * leur origine, et la règle de temps est déléguée pour qu'ils ne puissent pas
 * diverger.
 */
final class ValiditeDunAvoir
{
    public function __construct(
        private readonly ValiditeDunCode $validiteDansLeTemps = new ValiditeDunCode(),
    ) {
    }

    public function estUtilisable(
        StatutDeCode $statut,
        DateTimeImmutable $emisLe,
        DateTimeImmutable $maintenant,
    ): bool {
        if ($statut === StatutDeCode::UTILISE) {
            return false;
        }

        return $this->validiteDansLeTemps->estValide($emisLe, $maintenant);
    }
}
