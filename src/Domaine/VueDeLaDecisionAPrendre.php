<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Un créneau sous le seuil de maintien, entre 6 et 24 heures de son départ
 * (bandeau d'action du G2). Le contrôle des 24 heures annulera seul si rien ne
 * change ; ce bandeau ne fait qu'en avertir le gérant avant l'échéance.
 */
final class VueDeLaDecisionAPrendre
{
    public function __construct(
        private readonly string $jour,
        private readonly string $heure,
        private readonly string $bateau,
        private readonly int $inscrits,
    ) {
    }

    public function jour(): string
    {
        return $this->jour;
    }

    public function heure(): string
    {
        return $this->heure;
    }

    public function bateau(): string
    {
        return $this->bateau;
    }

    public function inscrits(): int
    {
        return $this->inscrits;
    }
}
