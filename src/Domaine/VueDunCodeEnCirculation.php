<?php

declare(strict_types=1);

namespace App\Domaine;

final class VueDunCodeEnCirculation
{
    /** @param int $montant en centimes */
    public function __construct(
        private readonly string $code,
        private readonly int $montant,
        private readonly string $origine,
        private readonly StatutDeCode $statut,
    ) {
    }

    public function code(): string
    {
        return $this->code;
    }

    public function montant(): int
    {
        return $this->montant;
    }

    /** « bon » ou « avoir ». */
    public function origine(): string
    {
        return $this->origine;
    }

    public function statut(): StatutDeCode
    {
        return $this->statut;
    }
}
