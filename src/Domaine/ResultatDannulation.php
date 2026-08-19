<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce que rend une demande d'annulation de créneau.
 *
 * Trois issues, et la distinction compte : annuler un créneau déjà annulé est
 * un **geste sans effet**, pas une faute, tandis qu'annuler une sortie déjà
 * partie est refusé (SPEC-CANCEL-02 AC-4 et AC-5). Confondre les deux
 * produirait soit une erreur bloquante injustifiée, soit un doublon d'envoi et
 * de remboursement.
 */
final class ResultatDannulation
{
    public const MOTIF_CRENEAU_DEJA_PASSE = 'CRENEAU_DEJA_PASSE';

    private function __construct(
        private readonly ?string $motifDuRefus,
        private readonly bool $sansEffet,
    ) {
    }

    public static function acceptee(): self
    {
        return new self(null, false);
    }

    /** Le créneau était déjà annulé : rien n'est rejoué. */
    public static function sansEffet(): self
    {
        return new self(null, true);
    }

    public static function refusee(string $motif): self
    {
        return new self($motif, false);
    }

    public function estAcceptee(): bool
    {
        return $this->motifDuRefus === null && !$this->sansEffet;
    }

    public function estSansEffet(): bool
    {
        return $this->sansEffet;
    }

    public function estRefusee(): bool
    {
        return $this->motifDuRefus !== null;
    }

    public function motifDuRefus(): ?string
    {
        return $this->motifDuRefus;
    }
}
