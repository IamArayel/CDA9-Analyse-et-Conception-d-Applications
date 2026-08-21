<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Une carte de sortie du tableau de bord (G2). Modèle de lecture, sans calcul.
 */
final class VueDeCarteDeSortie
{
    public function __construct(
        private readonly string $jour,
        private readonly string $heure,
        private readonly string $bateau,
        private readonly string $type,
        private readonly StatutDeLaCarteDeSortie $statut,
        private readonly int $inscrits,
        private readonly int $capacite,
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

    public function type(): string
    {
        return $this->type;
    }

    public function statut(): StatutDeLaCarteDeSortie
    {
        return $this->statut;
    }

    public function inscrits(): int
    {
        return $this->inscrits;
    }

    public function capacite(): int
    {
        return $this->capacite;
    }
}
