<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

/**
 * Un bateau de la flotte (SPEC-ADMIN-05).
 *
 * `forfaitDePrivatisation` est **nullable** : le formulaire de création n'en
 * demande pas, alors que la privatisation est tarifée par bateau. Un bateau
 * sans forfait n'est simplement pas proposé à la privatisation, ce qui règle la
 * contradiction relevée en revue du modèle de données.
 *
 * Aucune annotation Doctrine ici : la correspondance avec la table vit dans
 * config/doctrine/, cf. architecture.md §2.
 */
class Bateau
{
    private ?int $id = null;

    /** @param int|null $forfaitDePrivatisation en centimes */
    public function __construct(
        private string $nom,
        private int $capacite,
        private ?int $forfaitDePrivatisation = null,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function nom(): string
    {
        return $this->nom;
    }

    public function capacite(): int
    {
        return $this->capacite;
    }

    /** En centimes, ou null si le bateau n'est pas privatisable. */
    public function forfaitDePrivatisation(): ?int
    {
        return $this->forfaitDePrivatisation;
    }

    public function definirLeForfaitDePrivatisation(int $forfaitEnCentimes): void
    {
        $this->forfaitDePrivatisation = $forfaitEnCentimes;
    }

    public function estPrivatisable(): bool
    {
        return $this->forfaitDePrivatisation !== null;
    }
}
