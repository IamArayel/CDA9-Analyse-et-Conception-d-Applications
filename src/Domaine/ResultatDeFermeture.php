<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce que rend l'ajout d'un jour de fermeture (SPEC-ADMIN-04 AC-4).
 *
 * Fermer une date déjà réservée est **accepté**, mais n'annule ni ne rembourse
 * rien : les réservations concernées sont listées au gérant, à lui de traiter
 * ces clients. C'est l'effet de bord relevé dans l'analyse d'impact, que le
 * client n'avait pas envisagé.
 */
final class ResultatDeFermeture
{
    /** @param list<string> $reservationsConcernees références des réservations existantes */
    public function __construct(
        private readonly bool $estAcceptee,
        private readonly array $reservationsConcernees,
    ) {
    }

    public function estAcceptee(): bool
    {
        return $this->estAcceptee;
    }

    /** @return list<string> */
    public function reservationsConcernees(): array
    {
        return $this->reservationsConcernees;
    }
}
