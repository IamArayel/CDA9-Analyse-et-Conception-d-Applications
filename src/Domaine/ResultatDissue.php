<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce que rend l'enregistrement d'une issue d'annulation client
 * (SPEC-ADMIN-06).
 *
 * `codeProduit()` ne vaut quelque chose que pour l'issue `AVOIR` : le report et
 * le remboursement ne produisent aucun code. `montantPropose()` porte le
 * montant retenu, qui vaut la totalité quand le créneau était en alerte météo,
 * l'alerte l'emportant sur le barème dégressif (AC-4).
 */
final class ResultatDissue
{
    public const MOTIF_ISSUE_DEJA_ENREGISTREE = 'ISSUE_DEJA_ENREGISTREE';

    private function __construct(
        private readonly ?string $codeProduit,
        private readonly int $montantPropose,
        private readonly ?string $motifDuRefus,
    ) {
    }

    public static function acceptee(int $montantPropose, ?string $codeProduit = null): self
    {
        return new self($codeProduit, $montantPropose, null);
    }

    public static function refusee(string $motif): self
    {
        return new self(null, 0, $motif);
    }

    public function estAcceptee(): bool
    {
        return $this->motifDuRefus === null;
    }

    public function estRefusee(): bool
    {
        return $this->motifDuRefus !== null;
    }

    public function motifDuRefus(): ?string
    {
        return $this->motifDuRefus;
    }

    public function codeProduit(): ?string
    {
        return $this->codeProduit;
    }

    /** En centimes. */
    public function montantPropose(): int
    {
        return $this->montantPropose;
    }
}
