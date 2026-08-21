<?php

declare(strict_types=1);

namespace App\Domaine;

/** Les réglages de l'espace de gestion, tels qu'affichés à l'écran. */
final class VueDesParametres
{
    public function __construct(
        private readonly string $heureDouverture,
        private readonly string $heureDeFermeture,
        private readonly string $heureDalerte,
        private readonly int $delaiDeConfirmationEnHeures,
        private readonly int $delaiDeRappelEnHeures,
    ) {
    }

    public function heureDouverture(): string
    {
        return $this->heureDouverture;
    }

    public function heureDeFermeture(): string
    {
        return $this->heureDeFermeture;
    }

    public function heureDalerte(): string
    {
        return $this->heureDalerte;
    }

    public function delaiDeConfirmationEnHeures(): int
    {
        return $this->delaiDeConfirmationEnHeures;
    }

    public function delaiDeRappelEnHeures(): int
    {
        return $this->delaiDeRappelEnHeures;
    }
}
