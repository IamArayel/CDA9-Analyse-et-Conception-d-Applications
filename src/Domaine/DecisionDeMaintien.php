<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce que rend le contrôle du seuil de maintien d'une sortie (SPEC-BOOKING-03).
 *
 * La décision est rendue par une règle pure, sans base ni réseau : c'est la
 * couche application qui en tire les conséquences, annulation de la sortie et
 * remboursement intégral de chaque client.
 */
final class DecisionDeMaintien
{
    private function __construct(
        private readonly bool $sortieEstMaintenue,
    ) {
    }

    public static function maintien(): self
    {
        return new self(true);
    }

    public static function annulation(): self
    {
        return new self(false);
    }

    public function sortieEstMaintenue(): bool
    {
        return $this->sortieEstMaintenue;
    }

    /** Une sortie annulée faute d'inscrits rembourse ; une sortie maintenue non. */
    public function remboursementEstDu(): bool
    {
        return !$this->sortieEstMaintenue;
    }
}
