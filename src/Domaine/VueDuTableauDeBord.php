<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Le tableau de bord du gérant pour une journée (G2). Modèle de lecture, sans
 * calcul : tout lui est fourni par `Application\ConsulterLaJournee`.
 */
final class VueDuTableauDeBord
{
    /**
     * @param list<VueDeCarteDeSortie>      $cartes
     * @param list<VueDeLaDecisionAPrendre> $decisions
     */
    public function __construct(
        private readonly string $date,
        private readonly int $inscritsDuJour,
        private readonly int $sortiesProgrammees,
        private readonly int $encaisse,
        private readonly int $soldeABord,
        private readonly array $cartes,
        private readonly array $decisions,
    ) {
    }

    public function date(): string
    {
        return $this->date;
    }

    public function inscritsDuJour(): int
    {
        return $this->inscritsDuJour;
    }

    public function sortiesProgrammees(): int
    {
        return $this->sortiesProgrammees;
    }

    public function decisionsAPrendre(): int
    {
        return count($this->decisions);
    }

    /** En centimes. */
    public function encaisse(): int
    {
        return $this->encaisse;
    }

    /** En centimes : ce qui reste dû, à encaisser à bord. */
    public function soldeABord(): int
    {
        return $this->soldeABord;
    }

    /** @return list<VueDeCarteDeSortie> */
    public function cartes(): array
    {
        return $this->cartes;
    }

    /** @return list<VueDeLaDecisionAPrendre> */
    public function decisions(): array
    {
        return $this->decisions;
    }
}
