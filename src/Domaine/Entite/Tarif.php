<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

/**
 * La grille tarifaire d'un type de sortie (SPEC-ADMIN-02).
 *
 * Modifiable par le gérant. Un tarif modifié ne rattrape jamais une réservation
 * existante : le montant est recopié sur la réservation à sa validation.
 */
class Tarif
{
    private ?int $id = null;

    /**
     * @param int $prixAdulte en centimes
     * @param int $prixEnfant en centimes
     */
    public function __construct(
        private string $typeDeSortie,
        private int $prixAdulte,
        private int $prixEnfant,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function typeDeSortie(): string
    {
        return $this->typeDeSortie;
    }

    /** En centimes. */
    public function prixAdulte(): int
    {
        return $this->prixAdulte;
    }

    /** En centimes. */
    public function prixEnfant(): int
    {
        return $this->prixEnfant;
    }

    public function modifier(int $prixAdulte, int $prixEnfant): void
    {
        $this->prixAdulte = $prixAdulte;
        $this->prixEnfant = $prixEnfant;
    }
}
