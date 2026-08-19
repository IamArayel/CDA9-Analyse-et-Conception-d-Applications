<?php

declare(strict_types=1);

namespace App\Domaine\Entite;

use App\Domaine\StatutDeCode;
use DateTimeImmutable;

/**
 * Un avoir émis par le gérant (SPEC-BOOKING-10, SPEC-ADMIN-06).
 *
 * Sa seule origine est l'issue « avoir » d'une annulation demandée par le
 * client, depuis la correction du 2026-08-14. Il se comporte comme un bon
 * cadeau : un montant, un an de validité, une seule utilisation, aucun
 * rattachement à un type de sortie.
 *
 * Deux tables plutôt qu'une, alors que les dispositifs ne diffèrent plus que
 * par leur origine : choix réversible documenté dans mcd-mld.md §5, en attente
 * de la réponse du client à la question 8 du §11.
 */
class Avoir
{
    private ?int $id = null;
    private StatutDeCode $statut = StatutDeCode::DISPONIBLE;

    /** @param int $montant en centimes */
    public function __construct(
        private string $code,
        private int $montant,
        private DateTimeImmutable $dateDemission,
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

    public function dateDemission(): DateTimeImmutable
    {
        return $this->dateDemission;
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

    public function marquerUtilise(): void
    {
        $this->statut = StatutDeCode::UTILISE;
    }
}
