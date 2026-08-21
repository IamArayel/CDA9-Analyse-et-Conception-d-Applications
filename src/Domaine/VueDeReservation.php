<?php

declare(strict_types=1);

namespace App\Domaine;

use DateTimeImmutable;

/**
 * Ce qu'une réservation donne à voir.
 *
 * `montantDu()` est le montant **recopié sur la réservation** à sa validation,
 * jamais relu dans la grille tarifaire : un client ne peut pas être débité d'un
 * montant différent de celui qui lui a été présenté (SPEC-BOOKING-06 AC-3).
 *
 * `issuesProposees()` est vide pour une annulation décidée par le gérant. Le
 * triptyque report, avoir, remboursement n'existe que pour une annulation
 * demandée par le client (SPEC-ADMIN-06).
 */
final class VueDeReservation
{
    /**
     * @param int                     $montantDu       en centimes
     * @param list<IssueDannulation>  $issuesProposees
     */
    public function __construct(
        private readonly StatutDeReservation $statut,
        private readonly int $montantDu,
        private readonly string $devise,
        private readonly string $telephoneMobile,
        private readonly DateTimeImmutable $depart,
        private readonly DateTimeImmutable $expireLe,
        private readonly array $issuesProposees,
        private readonly ?string $avoirProduit,
        private readonly int $montantVerse = 0,
        private readonly int $soldeDu = 0,
        private readonly int $nombreDAdultes = 0,
        private readonly int $nombreDEnfants = 0,
        private readonly int $acompteAPayer = 0,
        private readonly int $tauxDAcompte = 0,
    ) {
    }

    /** En centimes : la somme des versements qui comptent encore. */
    public function montantVerse(): int
    {
        return $this->montantVerse;
    }

    /**
     * En centimes : ce qui reste à régler. Vaut zéro dès qu'un code a couvert
     * le prix, et dès que le créneau est annulé.
     */
    public function soldeDu(): int
    {
        return $this->soldeDu;
    }

    public function estSoldee(): bool
    {
        return $this->soldeDu === 0;
    }

    public function statut(): StatutDeReservation
    {
        return $this->statut;
    }

    /** En centimes. */
    public function montantDu(): int
    {
        return $this->montantDu;
    }

    /** Toujours l'euro : aucune conversion, quelle que soit la langue choisie. */
    public function devise(): string
    {
        return $this->devise;
    }

    /** Normalisé, sans point, tiret ni espace (SPEC-BOOKING-01 AC-8). */
    public function telephoneMobile(): string
    {
        return $this->telephoneMobile;
    }

    public function depart(): DateTimeImmutable
    {
        return $this->depart;
    }

    /** L'échéance de l'immobilisation des places, cf. ADR-003. */
    public function expireLe(): DateTimeImmutable
    {
        return $this->expireLe;
    }

    /** @return list<IssueDannulation> */
    public function issuesProposees(): array
    {
        return $this->issuesProposees;
    }

    /** Le code d'avoir émis pour cette réservation, s'il en existe un. */
    public function avoirProduit(): ?string
    {
        return $this->avoirProduit;
    }

    public function nombreDAdultes(): int
    {
        return $this->nombreDAdultes;
    }

    public function nombreDEnfants(): int
    {
        return $this->nombreDEnfants;
    }

    /** En centimes : ce que la réservation demande à l'instant de la réservation. */
    public function acompteAPayer(): int
    {
        return $this->acompteAPayer;
    }

    /** En pourcentage : 30 pour une sortie, 50 pour une privatisation. */
    public function tauxDAcompte(): int
    {
        return $this->tauxDAcompte;
    }
}
