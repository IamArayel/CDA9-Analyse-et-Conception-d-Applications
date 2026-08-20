<?php

declare(strict_types=1);

namespace App\Domaine;

/**
 * Ce que rend une tentative de paiement.
 *
 * Trois refus possibles, et ils ne se confondent pas : le prestataire a dit
 * non, la place a été vendue pendant que le client payait (SPEC-BOOKING-07
 * AC-7), ou le créneau a été annulé entre-temps (SPEC-CANCEL-03 AC-4).
 */
final class ResultatDePaiement
{
    public const MOTIF_TRANSACTION_REFUSEE = 'TRANSACTION_REFUSEE';
    public const MOTIF_PLACES_INSUFFISANTES = 'PLACES_INSUFFISANTES';
    public const MOTIF_CRENEAU_ANNULE = 'CRENEAU_ANNULE';
    public const MOTIF_HORS_FENETRE = 'HORS_FENETRE';
    public const MOTIF_RIEN_A_REGLER = 'RIEN_A_REGLER';

    private function __construct(private readonly ?string $motifDuRefus)
    {
    }

    public static function confirme(): self
    {
        return new self(null);
    }

    public static function refuse(string $motif): self
    {
        return new self($motif);
    }

    public function estConfirme(): bool
    {
        return $this->motifDuRefus === null;
    }

    public function estRefuse(): bool
    {
        return $this->motifDuRefus !== null;
    }

    public function motifDuRefus(): ?string
    {
        return $this->motifDuRefus;
    }
}
