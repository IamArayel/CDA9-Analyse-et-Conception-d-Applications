<?php

declare(strict_types=1);

namespace App\Domaine;

final class VueDunBateau
{
    /** @param int|null $forfaitDePrivatisation en centimes */
    public function __construct(
        private readonly string $nom,
        private readonly int $capacite,
        private readonly ?int $forfaitDePrivatisation,
    ) {
    }

    public function nom(): string
    {
        return $this->nom;
    }

    public function capacite(): int
    {
        return $this->capacite;
    }

    public function forfaitDePrivatisation(): ?int
    {
        return $this->forfaitDePrivatisation;
    }

    public function estPrivatisable(): bool
    {
        return $this->forfaitDePrivatisation !== null;
    }
}
