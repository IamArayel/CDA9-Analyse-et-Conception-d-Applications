<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use App\Domaine\StatutDeCode;
use DateTimeImmutable;

/**
 * Un bon cadeau acheté par un tiers (SPEC-BOOKING-09).
 *
 * Il vaut un montant, pas une sortie : depuis la v4 il ne porte ni type de
 * sortie ni catégorie de passager. Il se consomme en une fois, et le surplus
 * est perdu.
 */
class BonCadeau
{
    private ?int $id = null;
    private StatutDeCode $statut = StatutDeCode::DISPONIBLE;

    /** @param int $montant en centimes */
    public function __construct(
        private string $code,
        private int $montant,
        private DateTimeImmutable $dateDachat,
        private DateTimeImmutable $dateDexpiration,
        private ?string $emailBeneficiaire = null,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    /** En centimes. */
    public function montant(): int
    {
        return $this->montant;
    }

    public function dateDachat(): DateTimeImmutable
    {
        return $this->dateDachat;
    }

    public function dateDexpiration(): DateTimeImmutable
    {
        return $this->dateDexpiration;
    }

    /**
     * L'adresse par laquelle joindre le bénéficiaire. Seule donnée personnelle
     * portée par un code, et la seule que la purge des trois mois doit épargner
     * tant que le code est vivant (SPEC-NFR-04 AC-4).
     */
    public function emailBeneficiaire(): ?string
    {
        return $this->emailBeneficiaire;
    }

    /** La purge n'efface pas le code, elle le rend anonyme. */
    public function anonymiser(): void
    {
        $this->emailBeneficiaire = null;
    }

    public function statut(): StatutDeCode
    {
        return $this->statut;
    }

    /** Consommé en une fois, quel que soit le reliquat. */
    public function marquerUtilise(): void
    {
        $this->statut = StatutDeCode::UTILISE;
    }
}
